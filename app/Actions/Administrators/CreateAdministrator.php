<?php

namespace App\Actions\Administrators;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class CreateAdministrator
{
    public function execute(
        User $actor,
        string $name,
        string $email,
        UserRole $role,
        bool $isActive,
        string $password,
    ): User {
        Gate::forUser($actor)->authorize('create', User::class);

        $administrator = new User;
        $administrator->forceFill([
            'name' => $name,
            'email' => mb_strtolower($email),
            'email_verified_at' => now(),
            'role' => $role,
            'is_active' => $isActive,
            'password' => Hash::make($password),
        ])->save();

        return $administrator;
    }
}
