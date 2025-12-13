<?php

namespace App\Http\Controllers;

use App\Actions\Project\CreateProject;
use App\Actions\Project\DeleteProject;
use App\Actions\Project\UpdateProject;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Business;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects for the current business.
     */
    public function index(Business $business): View
    {
        $this->authorize('viewAny', [Project::class, $business]);

        $projects = $business->projects()
            ->with('creator')
            ->latest()
            ->paginate(12);

        return view('projects.index', compact('business', 'projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(Business $business): View
    {
        $this->authorize('create', [Project::class, $business]);

        return view('projects.create', compact('business'));
    }

    /**
     * Store a newly created project.
     */
    public function store(Business $business, StoreProjectRequest $request, CreateProject $action): RedirectResponse
    {
        $this->authorize('create', [Project::class, $business]);

        $project = $action->execute($business, auth()->user(), $request->validated());

        return redirect()
            ->route('businesses.projects.show', [$business, $project])
            ->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified project.
     */
    public function show(Business $business, Project $project): View
    {
        $this->authorize('view', $project);

        $project->load('creator');

        return view('projects.show', compact('business', 'project'));
    }

    /**
     * Show the form for editing the project.
     */
    public function edit(Business $business, Project $project): View
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('business', 'project'));
    }

    /**
     * Update the specified project.
     */
    public function update(Business $business, Project $project, UpdateProjectRequest $request, UpdateProject $action): RedirectResponse
    {
        $this->authorize('update', $project);

        $action->execute($project, $request->validated());

        return redirect()
            ->route('businesses.projects.show', [$business, $project])
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Business $business, Project $project, DeleteProject $action): RedirectResponse
    {
        $this->authorize('delete', $project);

        $action->execute($project);

        return redirect()
            ->route('businesses.projects.index', $business)
            ->with('success', 'Project deleted successfully!');
    }
}
