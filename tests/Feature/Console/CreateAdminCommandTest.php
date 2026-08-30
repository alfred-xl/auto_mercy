<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates an active administrator without exposing the password', function () {
    $password = 'Secure!Admin123';

    $this->artisan('app:create-admin')
        ->expectsQuestion('Name', 'Mercy Admin')
        ->expectsQuestion('Email address', 'ADMIN@EXAMPLE.COM')
        ->expectsChoice('Role', UserRole::SuperAdmin->value, array_column(UserRole::cases(), 'value'))
        ->expectsQuestion('Password', $password)
        ->expectsQuestion('Confirm password', $password)
        ->doesntExpectOutputToContain($password)
        ->assertSuccessful();

    $user = User::query()->where('email', 'admin@example.com')->firstOrFail();

    expect($user->role)->toBe(UserRole::SuperAdmin)
        ->and($user->is_active)->toBeTrue()
        ->and(Hash::check($password, $user->password))->toBeTrue();
});

it('rejects a weak password', function () {
    $this->artisan('app:create-admin')
        ->expectsQuestion('Name', 'Mercy Admin')
        ->expectsQuestion('Email address', 'admin@example.com')
        ->expectsChoice('Role', UserRole::InventoryManager->value, array_column(UserRole::cases(), 'value'))
        ->expectsQuestion('Password', 'weak')
        ->expectsQuestion('Confirm password', 'weak')
        ->assertFailed();

    expect(User::query()->count())->toBe(0);
});
