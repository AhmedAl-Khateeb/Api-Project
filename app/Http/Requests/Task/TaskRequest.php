<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
            'assignee_id' => 'required|exists:users,id',

            'dependencies' => 'nullable|array|distinct',
            'dependencies.*' => 'integer|exists:tasks,id',
        ];
    }

    public function messages(): array
    {
        return [
            'assignee_id.exists' => 'Selected user does not exist.',
            'due_date.after_or_equal' => 'Due date cannot be in the past.',
        ];
    }
}
