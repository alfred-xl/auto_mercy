<?php

namespace App\Policies;

use App\Models\CarImage;
use App\Models\User;

class CarImagePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CarImage $carImage): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CarImage $carImage): bool
    {
        return true;
    }

    public function delete(User $user, CarImage $carImage): bool
    {
        return true;
    }

    public function restore(User $user, CarImage $carImage): bool
    {
        return false;
    }

    public function forceDelete(User $user, CarImage $carImage): bool
    {
        return false;
    }
}
