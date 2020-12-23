<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    protected $taskService;

    public function setUp(): void
    {
        parent::setUp();

        $this->taskService = $this->app->make(TaskService::class);
    }

    public function testTaskCanBeCreated()
    {
        $user = User::factory()->create();
        $data = Task::factory()->raw([
            'author_id' => $user->id,
        ]);

        $task = $this->taskService->createTask($data);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertDatabaseCount('tasks', 1);
    }

    public function testTaskCanBeUpdated()
    {
        $user = User::factory()->create();
        $originalTask = Task::factory()->create([
            'author_id' => $user->id,
        ]);

        $updateData = Task::factory()->raw();

        $updatedTask = $this->taskService->updateTask($originalTask, $updateData);

        $this->assertEquals($updatedTask->name, $updateData['name']);
        $this->assertEquals($updatedTask->description, $updateData['description']);
        $this->assertEquals($updatedTask->due_date, $updateData['due_date']);
    }

    public function testTaskCanBeDeleted()
    {
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'author_id' => $user->id,
        ]);

        $result = $this->taskService->deleteTask($tasks->first()->id);

        $this->assertEquals($result, 1);
        $this->assertDatabaseCount('tasks', 2);
    }

    public function testFilterProjects()
    {
        $user = User::factory()->create();
        $project = Project::factory()->count(2)->create();

        $taskA = Task::factory()->create([
            'author_id' => 1,
            'project_id' => 1,
            'name' => 'First Task',
        ]);

        $taskB = Task::factory()->create([
            'author_id' => 1,
            'project_id' => 1,
            'name' => 'Second Task',
        ]);

        $taskC = Task::factory()->create([
            'author_id' => 1,
            'project_id' => 2,
            'name' => 'Third Task',
        ]);

        $filteredByName = $this->taskService->filterTasks([
            'search' => 'Third',
        ]);

        $filteredByProject = $this->taskService->filterTasks([
            'project_id' => 1,
        ]);

        $this->assertEquals($filteredByName->count(), 1);
        $this->assertTrue($filteredByName->contains('id', $taskC->id));

        $this->assertEquals($filteredByProject->count(), 2);
        $this->assertTrue($filteredByProject->contains('id', $taskA->id));
        $this->assertTrue($filteredByProject->contains('id', $taskB->id));
    }
}
