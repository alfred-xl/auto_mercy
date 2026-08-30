<?php

namespace App\Policies;

use App\Models\BodyType;
use App\Models\User;

class BodyTypePolicy extends AdministratorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->inventoryManager($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BodyType $bodyType): bool
    {
        return $this->inventoryManager($user);
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
    public function update(User $user, BodyType $bodyType): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BodyType $bodyType): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BodyType $bodyType): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BodyType $bodyType): bool
    {
        return false;
    }
}
