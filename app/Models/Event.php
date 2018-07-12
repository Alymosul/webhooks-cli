<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     * The associated table.
     *
     * @var string
     */
    protected $table = 'events';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Event can have many subscribed webhooks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }

    /**
     * Adds a new webhook to the event.
     *
     * @param string $callbackUrl
     *
     * @return Webhook|Model
     */
    public function addWebhook(string $callbackUrl)
    {
        return $this->webhooks()->create([
            'event_id' => $this->id,
            'callback_url' => $callbackUrl
        ]);
    }
}
