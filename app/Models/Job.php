<?php

namespace App\Models;

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
        'message', 'status', 'last_call_at', 'retries', 'retry_at'
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
        return static::where('status', '=', 'inprogress')
                     ->orWhere('retry_at', '=<', Carbon::now()->toDateTimeString())
                     ->get();
    }

    /**
     * Updates the status of the job to successful.
     *
     * @return bool
     */
    public function markAsSuccessful()
    {
        return $this->update([
            'status' => 'successful',
            'last_call_at' => Carbon::now(),
            'retries' => null,
            'retries_at' => null,
        ]);
    }

    /**
     * Updates the status of the job to fail.
     *
     * @param int $numberOfFailures
     * @param Carbon $nextRetryTime
     *
     * @return bool
     */
    public function markAsFailed(int $numberOfFailures, Carbon $nextRetryTime)
    {
        return $this->update([
            'status' => 'fail',
            'last_call_at' => Carbon::now(),
            'retries' => $numberOfFailures + 1,
            'retry_at' => $nextRetryTime
        ]);
    }
}
