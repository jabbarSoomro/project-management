<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private ProjectService $projectService
    ) {
    }

    public function store(int $projectId, CreateTaskRequest $request): JsonResponse
    {
        $project = $this->projectService->getProjectById($projectId);

        if (!$project) {
            return response()->json([
                'message' => 'Project not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('create', \App\Models\Task::class);

        $data = $request->validated();
        $data['project_id'] = $projectId;

        $task = $this->taskService->createTask($data);

        return response()->json([
            'message' => 'Task created successfully',
            'data' => new TaskResource($task->load('assignedUser')),
        ], Response::HTTP_CREATED);
    }
}
