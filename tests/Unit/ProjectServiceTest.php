<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Services\ProjectService;
use Tests\TestCase;

class ProjectServiceTest extends TestCase
{

    protected $projectService;

    public function setUp(): void
    {
        parent::setUp();

        $this->projectService = $this->app->make(ProjectService::class);
    }

    public function testProjectCanBeCreated()
    {
        $data = Project::factory()->raw();

        $project = $this->projectService->createProject($data);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertDatabaseCount('projects', 1);
    }

    public function testProjectCanBeUpdated()
    {
        $originalProject = Project::factory()->create();
        $updateData = Project::factory()->raw();

        $updatedProject = $this->projectService->updateProject($originalProject, $updateData);

        $this->assertEquals($updatedProject->name, $updateData['name']);
        $this->assertEquals($updatedProject->description, $updateData['description']);
    }

    public function testProjectCanBeDeleted()
    {
        $projects = Project::factory()->count(3)->create();

        $result = $this->projectService->deleteProject($projects->first()->id);

        $this->assertEquals($result, 1);
        $this->assertDatabaseCount('projects', 2);
    }

    public function testFilterProjects()
    {
        Project::factory()->count(3)->create();

        $projectA = Project::factory()->create([
            'name' => 'Test A',
        ]);

        $projectB = Project::factory()->create([
            'name' => 'Test B',
        ]);

        $filteredProjects = $this->projectService->filterProjects([
            'search' => 'test',
        ]);

        $this->assertEquals($filteredProjects->count(), 2);
        $this->assertTrue($filteredProjects->contains('id', $projectA->id));
        $this->assertTrue($filteredProjects->contains('id', $projectB->id));
    }
}
