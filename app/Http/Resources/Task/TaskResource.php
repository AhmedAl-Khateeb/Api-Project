<?php

namespace App\Http\Resources\Task;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'assignee' => [
                'id' => $this->assignee_id,
                'name' => $this->assignee?->name,
            ],
            'created_by' => $this->creator?->name,

            // pivot table for dependencies
            'dependencies' => $this->dependencies->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'depends_on_task_id' => $task->pivot->depends_on_task_id,
                ];
            }),
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
            'updated_at' => Carbon::parse($this->updated_at)->toDateTimeString(),
        ];
    }
}
