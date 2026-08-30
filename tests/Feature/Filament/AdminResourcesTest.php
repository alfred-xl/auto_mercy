<?php

use App\Filament\Resources\BodyTypes\BodyTypeResource;
use App\Filament\Resources\CarModels\CarModelResource;
use App\Filament\Resources\Cars\CarResource;
use App\Filament\Resources\CarStands\CarStandResource;
use App\Filament\Resources\Features\FeatureResource;
use App\Filament\Resources\Makes\MakeResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\BodyType;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarStand;
use App\Models\Feature;
use App\Models\Make;
use App\Models\User;

it('renders the phase four resources for a super administrator', function () {
    $this->actingAs(User::factory()->superAdmin()->create());

    $this->get(CarResource::getUrl('index'))->assertOk();
    $this->get(CarResource::getUrl('create'))->assertOk();
    $car = Car::factory()->create();
    $this->get(CarResource::getUrl('view', ['record' => $car]))->assertOk();
    $this->get(CarResource::getUrl('edit', ['record' => $car]))->assertOk();
    $this->get(MakeResource::getUrl('index'))->assertOk();
    $this->get(UserResource::getUrl('index'))->assertOk();

    $references = [
        [MakeResource::class, Make::factory()->create()],
        [CarModelResource::class, CarModel::factory()->create()],
        [BodyTypeResource::class, BodyType::factory()->create()],
        [FeatureResource::class, Feature::factory()->create()],
        [CarStandResource::class, CarStand::factory()->create()],
    ];

    foreach ($references as [$resource, $record]) {
        $this->get($resource::getUrl('index'))->assertOk();
        $this->get($resource::getUrl('view', ['record' => $record]))->assertOk();
        $this->get($resource::getUrl('edit', ['record' => $record]))->assertOk();
    }
});

it('enforces inventory manager resource boundaries on direct routes', function () {
    $this->actingAs(User::factory()->inventoryManager()->create());

    $this->get(CarResource::getUrl('create'))->assertOk();
    $this->get(MakeResource::getUrl('index'))->assertOk();
    $this->get(MakeResource::getUrl('create'))->assertForbidden();
    $this->get(UserResource::getUrl('index'))->assertForbidden();
});
