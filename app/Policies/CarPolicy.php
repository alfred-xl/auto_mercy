<?php

namespace App\Policies;

use App\Models\Car;
use App\Models\User;

class CarPolicy extends AdministratorPolicy
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
    public function view(User $user, Car $car): bool
    {
        return $this->inventoryManager($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->inventoryManager($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Car $car): bool
    {
        return $this->inventoryManager($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Car $car): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Car $car): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Car $car): bool
    {
        return false;
    }

    public function publish(User $user, Car $car): bool
    {
        return $this->inventoryManager($user);
    }

    public function transitionStatus(User $user, Car $car): bool
    {
        return $this->inventoryManager($user);
    }

    public function archive(User $user, Car $car): bool
    {
        return $this->inventoryManager($user);
    }
}
