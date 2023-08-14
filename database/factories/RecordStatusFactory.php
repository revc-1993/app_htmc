<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecordStatus>
 */
class RecordStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'status' => $this->faker->unique()->randomElement([
                "PENDIENTE DE REVISIÓN",
                "EN REVISIÓN",
                "OBSERVADO",
                "DEVUELTO",
                "REGISTRADO",
                "APROBADO",
                "ANULADO",
                "LIQUIDADO",
            ]),
        ];
    }
}
