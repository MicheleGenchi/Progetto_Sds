<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_code' => 'IT',
            'postal_code' => '74123',
            'position' => 'Taranto',
            'region' => 'Puglia',
            'region_code' => '',
            'province' => 'Taranto',
            'sigle_province' => 'TA',
            'latitude' => 40.4644,
            'longitude' => 17.2471
        ];
    }
}
