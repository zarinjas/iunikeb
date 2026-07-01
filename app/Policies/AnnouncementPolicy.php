<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use App\Support\AccessControl;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(AccessControl::PERMISSION_VIEW_ANNOUNCEMENTS);
    }

    public function view(User $user, Announcement $announcement): bool
    {
        if (! $user->can(AccessControl::PERMISSION_VIEW_ANNOUNCEMENTS)) {
            return false;
        }

        return $announcement->cooperative_id === $user->cooperative_id;
    }

    public function create(User $user): bool
    {
        return $user->can(AccessControl::PERMISSION_CREATE_ANNOUNCEMENTS);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        if (! $user->can(AccessControl::PERMISSION_EDIT_ANNOUNCEMENTS)) {
            return false;
        }

        return $announcement->cooperative_id === $user->cooperative_id;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        if (! $user->can(AccessControl::PERMISSION_DELETE_ANNOUNCEMENTS)) {
            return false;
        }

        return $announcement->cooperative_id === $user->cooperative_id;
    }

    public function publish(User $user, Announcement $announcement): bool
    {
        if (! $user->can(AccessControl::PERMISSION_PUBLISH_ANNOUNCEMENTS)) {
            return false;
        }

        return $announcement->cooperative_id === $user->cooperative_id;
    }

    public function viewReadStats(User $user, Announcement $announcement): bool
    {
        if (! $user->can(AccessControl::PERMISSION_VIEW_ANNOUNCEMENTS)) {
            return false;
        }

        return $announcement->cooperative_id === $user->cooperative_id;
    }
}
