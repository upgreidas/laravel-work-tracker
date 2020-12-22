<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Arr;

class ProjectService
{

    public function filterProjects(array $filter = [])
    {
        $query = Project::query();

        if (Arr::has($filter, 'search')) {
            $query->where('name', 'LIKE', '%' . $filter['search'] . '%');
        }

        return $query->orderBy('name')->get();
    }

    public function createProject($input)
    {
        $data = Arr::only($input, [
            'name',
            'description',
        ]);

        $project = Project::create($data);

        return $project;
    }

    public function updateProject(Project $project, $input)
    {
        $data = Arr::only($input, [
            'name',
            'description',
        ]);

        $project->update($data);

        return $project;
    }

    public function deleteProject($projectId)
    {
        return Project::destroy($projectId);
    }

}
