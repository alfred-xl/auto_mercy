<?php

namespace App\Actions\Administrators;

use App\Enums\UserRole;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UpdateAdministrator
{
    public function execute(
        User $administrator,
        User $actor,
        string $name,
        string $email,
        UserRole $role,
        bool $isActive,
        ?string $password = null,
    ): User {
        Gate::forUser($actor)->authorize('update', $administrator);

        return DB::transaction(function () use ($administrator, $actor, $name, $email, $role, $isActive, $password): User {
            $lockedAdministrator = User::query()->lockForUpdate()->findOrFail($administrator->getKey());

            if ($lockedAdministrator->is($actor) && ! $isActive) {
                throw new DomainException('You cannot deactivate your own administrator account.');
            }

            $removesActiveSuperAdmin = $lockedAdministrator->role === UserRole::SuperAdmin
                && $lockedAdministrator->is_active
                && ($role !== UserRole::SuperAdmin || ! $isActive);

            if ($removesActiveSuperAdmin) {
                $activeSuperAdmins = User::query()
                    ->where('role', UserRole::SuperAdmin)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->get(['id']);

                if ($activeSuperAdmins->count() === 1) {
                    throw new DomainException('The final active Super Administrator cannot be demoted or deactivated.');
                }
            }

            $attributes = [
                'name' => $name,
                'email' => mb_strtolower($email),
                'role' => $role,
                'is_active' => $isActive,
            ];

            if (filled($password)) {
                $attributes['password'] = Hash::make($password);
            }

            $lockedAdministrator->forceFill($attributes)->save();

            return $lockedAdministrator->refresh();
        });
    }
}
