<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'start_date',
        'due_date',
    ];

    protected $appends = ['total_time'];

    protected $casts = [
        'start_date' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function timeEntries()
    {
        return $this->morphMany(TimeEntry::class, 'registrable');
    }

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subTasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function getTotalTimeAttribute()
    {
        return $this->timeEntries->sum(function ($entry) {
            return $entry->seconds_elapsed;
        });
    }
}
