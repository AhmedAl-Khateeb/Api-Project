<?php

namespace App\Http\Controllers\Task;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskRequest;
use App\Http\Requests\Task\UpdateStatusRequest;
use App\Http\Resources\Task\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly ApiResponse $apiResponse,
        readonly private TaskService $taskService)
    {
    }

    // Show All
    public function index(Request $request)
    {
        $tasks = $this->taskService->index(
            $request->only(['status', 'from', 'to', 'assignee_id'])
        );

        return $this->apiResponse->success(
            TaskResource::collection($tasks)
        );
    }

    // Show One
    public function show(int $id)
    {
        $task = $this->taskService->show($id);

        return $this->apiResponse->success(
            new TaskResource($task)
        );
    }

    // Create Task
    public function store(TaskRequest $request)
    {
        try {
            $task = $this->taskService->store($request->validated());

            return $this->apiResponse->created(null, 'تم إنشاء المهمة بنجاح');
        } catch (\Throwable $th) {
            return $this->apiResponse->error($th->getMessage(), 400);
        }
    }

    // Update Task
    public function update(TaskRequest $request, int $id)
    {
        try {
            $task = $this->taskService->update($request->validated(), $id);

            return $this->apiResponse->updated(null, 'تم تعديل المهمة بنجاح');
        } catch (\Throwable $th) {
            return $this->apiResponse->error($th->getMessage(), 400);
        }
    }

    // Delete Task
    public function destroy(int $id)
    {
        try {
            $this->taskService->destroy($id);

            return $this->apiResponse->deleted();
        } catch (\Throwable $th) {
            return $this->apiResponse->error($th->getMessage(), 400);
        }
    }

    // Change Status
    public function changeStatus(UpdateStatusRequest $request, int $id)
    {
        try {
            $task = $this->taskService->changeStatus($id, $request->validated()['status']);

            return $this->apiResponse->updated(null, 'تم تغيير حالة المهمة بنجاح');
        } catch (\Throwable $th) {
            return $this->apiResponse->error($th->getMessage(), 400);
        }
    }
}
