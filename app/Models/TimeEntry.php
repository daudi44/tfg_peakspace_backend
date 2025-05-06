<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'registrable_type',
        'registrable_id',
    ];

    protected $appends = ['seconds_elapsed'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrable()
    {
        return $this->morphTo();
    }

    public function getSecondsElapsedAttribute()
{
    if ($this->end_time) {
        return $this->start_time->diffInSeconds($this->end_time);
    }

    return $this->start_time->diffInSeconds(now());
}
}
