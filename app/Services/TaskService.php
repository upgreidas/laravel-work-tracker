<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Arr;

class TaskService
{
    public function filterTasks(array $filter = [], $itemsPerPage = 20)
    {
        $query = Task::query();

        if (Arr::has($filter, 'search')) {
            $query->where('name', 'LIKE', '%' . $filter['search'] . '%');
        }

        if (Arr::has($filter, 'project_id')) {
            $query->where('project_id', $filter['project_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($itemsPerPage);
    }

    public function createTask($input)
    {
        $data = Arr::only($input, [
            'author_id',
            'assignee_id',
            'project_id',
            'name',
            'description',
            'due_date',
        ]);

        $project = Task::create($data);

        return $project;
    }

    public function updateTask(Task $task, $input)
    {
        $data = Arr::only($input, [
            'assignee_id',
            'project_id',
            'name',
            'description',
            'due_date',
        ]);

        $task->update($data);

        return $task;
    }

    public function deleteTask($taskId)
    {
        return Task::destroy($taskId);
    }
}
