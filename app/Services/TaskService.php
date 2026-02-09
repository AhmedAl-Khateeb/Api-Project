<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function index(array $filters = [])
    {
        return Task::with(['assignee', 'creator', 'dependencies'])
            ->when($filters['status'] ?? null,
                fn ($q, $status) => $q->where('status', $status)
            )
            ->when(isset($filters['from'], $filters['to']),
                fn ($q) => $q->whereBetween('due_date', [$filters['from'], $filters['to']])
            )
            ->when($filters['assignee_id'] ?? null,
                fn ($q, $assigneeId) => $q->where('assignee_id', $assigneeId)
            )
            ->when(auth('sanctum')->user()->hasRole('user'),
                fn ($q) => $q->where('assignee_id', auth('sanctum')->id())
            )
            ->latest()
            ->paginate(10);
    }

    // one task
    public function show(int $id)
    {
        $query = Task::with(['assignee', 'creator', 'dependencies'])->where('id', $id);

        if (auth('sanctum')->user()->hasRole('user')) {
            $query->where('assignee_id', auth('sanctum')->id());
        }

        return $query->firstOrFail();
    }

    public function store(array $data)
    {
        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'assignee_id' => $data['assignee_id'],
            'created_by' => auth('sanctum')->id(),
        ]);

        // paivotable for dependencies
        if (!empty($data['dependencies'])) {
            $task->dependencies()->sync($data['dependencies']);
        }

        return $task->load('dependencies');
    }

    // update task
    public function update(array $data, int $id)
    {
        $task = Task::findOrFail($id);

        $task->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'assignee_id' => $data['assignee_id'],
[]        ]);

        // paivotable for dependencies
        if (!empty($data['dependencies'])) {
            $task->dependencies()->sync($data['dependencies']);
        }

        return $task->load('dependencies');
    }

    // Delete task
    public function destroy(int $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return true;
    }

    // Update Status
    public function changeStatus(int $id, string $status)
    {
        $task = Task::with('dependencies')->findOrFail($id);

        $user = auth('sanctum')->user();

        $isManager = $user->hasRole('manager');

        if (!$isManager && $task->assignee_id !== $user->id) {
            throw new AuthorizationException('Not allowed.');
        }

        if ($status === 'completed') {
            $hasUncompletedDeps = $task->dependencies()
                ->where('status', '!=', 'completed')
                ->exists();
            if ($hasUncompletedDeps) {
                throw ValidationException::withMessages(['status' => 'Task has uncompleted dependencies']);
            }
        }

        $task->update(['status' => $status]);

        return $task->load('dependencies', 'assignee');
    }
}
