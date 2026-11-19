<?php

namespace App\Policies;

use App\Models\ProjectManagementProject;
use App\Models\ProjectManagementTask;
use App\Models\User;

class ProjectManagementProjectPolicy
{
    public function view(User $user, ProjectManagementProject $project): bool
    {
        return $this->isParticipant($user, $project);
    }

    public function update(User $user, ProjectManagementProject $project): bool
    {
        return $project->project_manager_id === $user->id;
    }

    public function delete(User $user, ProjectManagementProject $project): bool
    {
        return $this->update($user, $project);
    }

    public function manageTasks(User $user, ProjectManagementProject $project): bool
    {
        return $this->update($user, $project);
    }

    public function viewTask(User $user, ProjectManagementProject $project, ProjectManagementTask $task): bool
    {
        return $task->project_id === $project->id && $this->isParticipant($user, $project);
    }

    public function commentTask(User $user, ProjectManagementProject $project, ProjectManagementTask $task): bool
    {
        return $this->viewTask($user, $project, $task);
    }

    public function viewMeetings(User $user, ProjectManagementProject $project): bool
    {
        return $this->isParticipant($user, $project);
    }

    public function createMeeting(User $user, ProjectManagementProject $project): bool
    {
        return $this->isParticipant($user, $project);
    }

    private function isParticipant(User $user, ProjectManagementProject $project): bool
    {
        return ProjectManagementProject::query()
            ->whereKey($project->getKey())
            ->visibleTo($user)
            ->exists();
    }
}
