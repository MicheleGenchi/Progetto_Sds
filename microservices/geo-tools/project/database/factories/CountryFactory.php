<?php

namespace Database\Factories;

use App\Models\GeoNazione;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GeoNazione>
 */
class CountrysFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_code' => 'IT',  //
            'country' => 'Italia'
        ];
    }
}
