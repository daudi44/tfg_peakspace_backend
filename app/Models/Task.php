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
        return $this->hasMany(TimeEntry::class);
    }
}
