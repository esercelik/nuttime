<?php

namespace App\Policies;

use App\Models\SiteSetting;
use App\Models\User;

final class SiteSettingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'manager'], true);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SiteSetting $siteSetting): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SiteSetting $siteSetting): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SiteSetting $siteSetting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SiteSetting $siteSetting): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SiteSetting $siteSetting): bool
    {
        return false;
    }
}
