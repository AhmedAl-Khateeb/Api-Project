<?php

namespace App\Trait;

use App\Models\Task;
use App\Models\User;

trait TaskTrait
{
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_task_id')
        ->withPivot('task_id', 'depends_on_task_id')->withTimestamps();
    }
}
