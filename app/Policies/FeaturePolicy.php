<?php

namespace App\Policies;

use App\Models\Feature;
use App\Models\User;

class FeaturePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Feature $feature): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Feature $feature): bool
    {
        return true;
    }

    public function delete(User $user, Feature $feature): bool
    {
        return ! $feature->cars()->exists();
    }

    public function restore(User $user, Feature $feature): bool
    {
        return false;
    }

    public function forceDelete(User $user, Feature $feature): bool
    {
        return false;
    }
}
