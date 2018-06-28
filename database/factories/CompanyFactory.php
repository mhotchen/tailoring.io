<?php

use App\Measurement\Settings\UnitOfMeasurementSetting;
use Faker\Generator as Faker;

$factory->define(App\Models\Company::class, function (Faker $faker) {
    return [
        'id' => $faker->uuid,
        'name' => $faker->company,
        'unit_of_measurement' => UnitOfMeasurementSetting::DEFAULT(),
    ];
});
