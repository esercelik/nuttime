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
        return $user->canPublishCms();
    }

    public function update(User $user, Model $model): bool
    {
        return $user->canEditCms();
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->canPublishCms();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canPublishCms();
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
        return $user->canEditCms();
    }
}
