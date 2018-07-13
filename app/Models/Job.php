<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Job extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'jobs';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message', 'status', 'last_call_at', 'retries', 'retry_at', 'locked'
    ];

    /**
     * Job belongs to only one webhook.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function webhook()
    {
        return $this->belongsTo(WebHook::class);
    }

    /**
     * Gets all the jobs that are in progress or the failed jobs that should be retried now.
     *
     * @return static[]
     */
    public static function getScheduledJobs()
    {
        return static::where('locked', '=', false)
                     ->where(function (Builder $query) {
                         $query->where('status', '=', 'inprogress')
                               ->orWhere('retry_at', '<=', Carbon::now()->toDateTimeString());
                     })->get();
    }

    /**
     * Detects if there are jobs that still not yet resolved.
     *
     * @return bool
     */
    public static function unresolvedJobsExist()
    {
        return static::where('retry_at', '!=', null)->exists();
    }

    /**
     * Updates the status of the job to successful.
     *
     * @return bool
     */
    public function markAsSuccessful()
    {
        return $this->update([
            'status' => 'success',
            'last_call_at' => Carbon::now(),
            'retries' => 0,
            'retry_at' => null,
        ]);
    }

    /**
     * Updates the status of the job to fail.
     *
     * @param int $numberOfFailures
     * @param Carbon|null $nextRetryTime
     *
     * @return bool
     */
    public function markAsFailed(int $numberOfFailures, Carbon $nextRetryTime = null)
    {
        return $this->update([
            'status' => 'fail',
            'last_call_at' => Carbon::now(),
            'retries' => $numberOfFailures + 1,
            'retry_at' => $nextRetryTime
        ]);
    }

    /**
     * Locks the job to be executed in the current process.
     *
     * @return bool
     */
    public function hold()
    {
        return $this->update(['locked' => true]);
    }

    /**
     * Unlocks the job to allow other process to execute it.
     *
     * @return bool
     */
    public function release()
    {
        return $this->update(['locked' => false]);
    }
}
