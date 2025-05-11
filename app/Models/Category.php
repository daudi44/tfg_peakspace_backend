<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type',
        'user_id',
        'parent_category_id',
    ];

    protected $appends = [
        'all_time',
        'total_tasks',
        'total_spent_money',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_category_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_category_id');
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function timeEntries()
    {
        return $this->morphMany(TimeEntry::class, 'registrable');
    }

    public function getAllTimeAttribute()
    {
        $childrenTime = $this->children()->get()->sum(function ($child) {
            return $child->timeEntries()->get()->sum('seconds_elapsed');
        });
        
        $tasksTime = $this->tasks()->get()->sum(function ($task) {
            return $task->timeEntries()->get()->sum('seconds_elapsed');
        });
        
        return $this->timeEntries()->get()->sum('seconds_elapsed') + $tasksTime + $childrenTime;
    }

    public function getTotalTasksAttribute()
    {
        $childrenTasks = $this->children()->get()->sum(function ($child) {
            return $child->tasks()->get()->count();
        });
        return $this->tasks()->get()->count() + $childrenTasks;
    }

    public function getTotalSpentMoneyAttribute()
    {
        $childrenSpent = $this->children()->get()->sum(function ($child) {
            return $child->total_spent_money;
        });
        $totalMovements = $this->movements()->get();

        $totalSpent = 0;
        foreach ($totalMovements as $movement) {
            if ($movement->type == 1) {
                $totalSpent += $movement->amount;
            } else {
                $totalSpent -= $movement->amount;
            }
        }

        return $totalSpent + $childrenSpent;
    }
}
