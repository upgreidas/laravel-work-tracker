<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUnauthenticatedUsersCannotManageProjects()
    {
        $getResponse = $this->getJson('/api/projects');
        $postResponse = $this->postJson('/api/projects');
        $patchResponse = $this->patchJson('/api/projects/1');
        $deleteResponse = $this->deleteJson('/api/projects/1');

        $getResponse->assertStatus(401);
        $postResponse->assertStatus(401);
        $patchResponse->assertStatus(401);
        $deleteResponse->assertStatus(401);
    }

    public function testUserCanGetProjectList()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $projects = Project::factory()->count(3)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/projects');

        $response->assertOk();
        $response->assertJsonStructure([
            'projects',
        ]);
        $response->assertJsonCount(3, 'projects');
        $response->assertJsonFragment([
            'name' => $projects->first()->name,
        ]);
    }

    public function testUserCanCreateProject()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $data = Project::factory()->raw();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/projects', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'project' => [
                'id',
                'name',
                'description',
            ],
        ]);
        $this->assertDatabaseCount('projects', 1);
    }

    public function testProjectNameIsRequired()
    {
        $user = User::factory()->create();
        $data = [];

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/projects', $data);

        $response->assertJsonValidationErrors([
            'name',
        ]);
    }

    public function testUserCanUpdateProject()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $project = Project::factory()->create();
        $data = Project::factory()->raw();

        $response = $this->actingAs($user, 'api')
            ->patchJson('/api/projects/' . $project->id, $data);

        $response->assertOk();
        $response->assertJson([
            'project' => [
                'name' => $data['name'],
                'description' => $data['description'],
            ],
        ]);
    }

    public function testUserCanDeleteProject()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $project = Project::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->deleteJson('/api/projects/' . $project->id);

        $response->assertOk();
        $response->assertExactJson([
            'project' => [
                'id' => $project->id,
            ],
        ]);
    }
}
