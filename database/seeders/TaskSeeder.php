<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $managerId = User::where('email', 'manager@example.com')->value('id');
        $userId = User::where('email', 'user@example.com')->value('id');

        if (!$managerId || !$userId) {
            throw new \Exception('Seed users first: manager/user not found.');
        }

        $task = new Task();

        $task->forceFill([
            'title' => 'Task 1',
            'description' => 'Description for task 1',
            'status' => 'pending',
            'due_date' => now()->addDays(2),
            'assignee_id' => $userId,
            'created_by' => $managerId,
        ])->save();
    }
}
