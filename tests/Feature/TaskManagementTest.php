<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    public function testUnauthenticatedUsersCannotManageTasks()
    {
        $getResponse = $this->getJson('/api/tasks');
        $postResponse = $this->postJson('/api/tasks');
        $patchResponse = $this->patchJson('/api/tasks/1');
        $deleteResponse = $this->deleteJson('/api/tasks/1');

        $getResponse->assertStatus(401);
        $postResponse->assertStatus(401);
        $patchResponse->assertStatus(401);
        $deleteResponse->assertStatus(401);
    }

    public function testUserCanGetPaginatedTasks()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'author_id' => 1,
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/tasks');

        $response->assertOk();
        $response->assertJsonStructure([
            'current_page',
            'total',
            'data',
        ]);
    }

    public function testUserCanCreateTask()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $data = Task::factory()->raw();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/tasks', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'task' => [
                'id',
                'name',
                'description',
                'status',
            ],
        ]);
        $this->assertDatabaseCount('tasks', 1);
    }

    public function testValidateThatTaskIsAssignedToExistingProject()
    {
        $user = User::factory()->create();
        $data = Task::factory()->raw([
            'project_id' => 1,
        ]);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/tasks', $data);

        $response->assertJsonValidationErrors([
            'project_id',
        ]);
    }

    public function testUserCanUpdateTask()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'author_id' => 1,
        ]);
        $data = Task::factory()->raw();

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/tasks/' . $task->id, $data);

        $response->assertOk();
        $response->assertJson([
            'task' => [
                'name' => $data['name'],
                'description' => $data['description'],
            ],
        ]);
    }

    public function testUserCanDeleteTask()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $task = Task::factory()->create([
            'author_id' => 1,
        ]);

        $response = $this->actingAs($user, 'api')
            ->deleteJson('/api/tasks/' . $task->id);

        $response->assertOk();
        $response->assertExactJson([
            'task' => [
                'id' => $task->id,
            ],
        ]);
    }
}
