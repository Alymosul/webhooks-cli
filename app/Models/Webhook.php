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
}
