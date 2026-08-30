<?php

use App\Models\Car;
use App\Models\CarModel;
use App\Models\Make;

it('rejects a model that does not belong to the selected make', function () {
    $selectedMake = Make::factory()->create();
    $model = CarModel::factory()->create();

    Car::factory()->create([
        'make_id' => $selectedMake->id,
        'car_model_id' => $model->id,
    ]);
})->throws(DomainException::class, 'does not belong');
