<?php

use App\Enums\UserRole;
use App\Models\User;

it('redirects guests to the admin login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('allows active approved administrators into the panel', function (string $state) {
    $user = User::factory()->{$state}()->create();

    $this->actingAs($user)->get('/admin')->assertOk();
})->with(['superAdmin', 'inventoryManager']);

it('denies inactive and unassigned users', function (array $state) {
    $user = User::factory()->create($state);

    $this->actingAs($user)->get('/admin')->assertForbidden();
})->with([
    'unassigned' => [[]],
    'inactive administrator' => [['role' => UserRole::SuperAdmin, 'is_active' => false]],
]);
