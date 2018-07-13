<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'webhooks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['event_name', 'callback_url'];

    /**
     * Webhook has only one scheduled job.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function job()
    {
        return $this->hasOne(Job::class);
    }

    /**
     * Schedules a job with the given message to the webhook.
     *
     * @param string $message
     *
     * @return Job|Model
     */
    public function schedule(string $message)
    {
        return $this->job()->create(['message' => $message]);
    }
}
