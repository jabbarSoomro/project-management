<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository
    ) {
    }

    public function createProject(array $data): Project
    {
        return $this->projectRepository->create($data);
    }

    public function getProjectById(int $id): ?Project
    {
        return $this->projectRepository->findById($id);
    }

    public function getProjectWithTasks(int $id): ?Project
    {
        return $this->projectRepository->findByIdWithTasks($id);
    }

    public function getAllProjects(): Collection
    {
        return $this->projectRepository->getAll();
    }

    public function getUserProjects(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->projectRepository->getPaginatedByUserId($userId, $perPage);
    }
}
