<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProcessType>
 */
class ProcessTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'process_type' => $this->faker->unique()->randomElement([
                'SUBASTA INVERSA ELECTRONICA', 'INFIMA CUANTIA', 'CATALOGO ELECTRONICO'
            ]),
        ];
    }
}
