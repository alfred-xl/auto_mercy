<?php

use App\Actions\Administrators\CreateAdministrator;
use App\Actions\Administrators\UpdateAdministrator;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates an administrator through the guarded action', function () {
    $actor = User::factory()->superAdmin()->create();
    $administrator = app(CreateAdministrator::class)->execute($actor, 'Inventory Lead', 'LEAD@EXAMPLE.COM', UserRole::InventoryManager, true, 'a-secure-password');

    expect($administrator->email)->toBe('lead@example.com')
        ->and($administrator->role)->toBe(UserRole::InventoryManager)
        ->and(Hash::check('a-secure-password', $administrator->password))->toBeTrue();
});

it('protects self deactivation and the final active super administrator', function () {
    $actor = User::factory()->superAdmin()->create();

    expect(fn () => app(UpdateAdministrator::class)->execute($actor, $actor, $actor->name, $actor->email, UserRole::SuperAdmin, false))
        ->toThrow(DomainException::class, 'cannot deactivate your own');

    $replacement = User::factory()->superAdmin()->create();
    app(UpdateAdministrator::class)->execute($replacement, $actor, $replacement->name, $replacement->email, UserRole::InventoryManager, true);
    expect($replacement->refresh()->role)->toBe(UserRole::InventoryManager);
});
