<?php

namespace App\Models;

use App\Trait\TaskTrait;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use TaskTrait;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assignee_id',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];


    // Automatically set the created_by field to the authenticated user's ID when creating a new task
    protected static function booted()
    {
        static::creating(function ($task) {
            if (empty($task->created_by)) {
                $task->created_by = auth()->id();
            }
        });
    }
}
