<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class attendance extends Model
{
    protected $fillable = [
        'user_id', 'date', 'am_time_in', 'am_time_out', 'pm_time_in', 'pm_time_out', 'hours_under_time', 'minutes_under_time', 'status'
    ];

    protected $casts = [
        'am_time_in' => 'datetime',
        'am_time_out' => 'datetime',
        'pm_time_in' => 'datetime',
        'pm_time_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
