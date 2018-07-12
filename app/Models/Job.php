<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
