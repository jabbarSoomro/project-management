<?php
namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    public function create(array $data): Project;
    public function findById(int $id): ?Project;
    public function findByIdWithTasks(int $id): ?Project;
    public function getAll(): Collection;
    public function getPaginatedByUserId(int $userId, int $perPage = 10): LengthAwarePaginator;
}
