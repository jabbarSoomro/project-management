<?php
namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function create(array $data): Project
    {
        return Project::create($data);
    }

    public function findById(int $id): ?Project
    {
        return Project::find($id);
    }

    public function findByIdWithTasks(int $id): ?Project
    {
        return Project::with(['tasks.assignedUser'])->find($id);
    }

    public function getAll(): Collection
    {
        return Project::with('tasks')->get();
    }

    public function getPaginatedByUserId(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Project::where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}
