<?php
namespace App\Services;

use App\Jobs\SendTaskNotificationJob;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;

class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository
    ) {
    }

    public function createTask(array $data): Task
    {
        $task = $this->taskRepository->create($data);

        // Dispatch job to send email notification
        SendTaskNotificationJob::dispatch($task);

        return $task;
    }

    public function getTaskById(int $id): ?Task
    {
        return $this->taskRepository->findById($id);
    }
}
