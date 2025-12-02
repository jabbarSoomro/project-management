<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {
    }

    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $projects = \App\Models\Project::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => \App\Http\Resources\ProjectResource::collection($projects),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
        ]);
    }

    public function store(CreateProjectRequest $request): JsonResponse
    {
        $this->authorize('create', \App\Models\Project::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $project = $this->projectService->createProject($data);

        return response()->json([
            'message' => 'Project created successfully',
            'data' => new ProjectResource($project),
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        $project = $this->projectService->getProjectWithTasks($id);

        if (!$project) {
            return response()->json([
                'message' => 'Project not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('view', $project);

        return response()->json([
            'data' => new ProjectResource($project),
        ]);
    }
}
