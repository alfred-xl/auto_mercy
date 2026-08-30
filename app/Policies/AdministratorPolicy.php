<?php

namespace App\Policies;

use App\Models\User;

abstract class AdministratorPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    protected function inventoryManager(User $user): bool
    {
        return $user->isInventoryManager();
    }
}
