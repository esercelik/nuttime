<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class CmsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageCms();
    }

    public function view(User $user, Model $model): bool
    {
        return $user->canManageCms();
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'manager', 'editor'], true);
    }

    public function update(User $user, Model $model): bool
    {
        return in_array($user->role, ['super_admin', 'manager', 'editor'], true);
    }

    public function delete(User $user, Model $model): bool
    {
        return in_array($user->role, ['super_admin', 'manager'], true);
    }

    public function deleteAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'manager'], true);
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->isSuperAdmin();
    }

    public function restoreAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->isSuperAdmin();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function reorder(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'manager', 'editor'], true);
    }
}
