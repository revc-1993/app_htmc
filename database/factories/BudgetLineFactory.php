<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BudgetLine>
 */
class BudgetLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'budget_line' => $this->faker->unique()->randomElement([
                '510105 - REMUNERACIONES UNIFICADAS',
                '510106 - SALARIOS UNIFICADOS',
                '510111 - REMUNERACIONES UNIFICADAS DE PROFESIONALES DE LA SALUD',
                '510203 - DECIMO TERCER SUELDO'
            ]),
        ];
    }
}
