<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseType>
 */
class ExpenseTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'expense_type' => $this->faker->unique()->randomElement([
                'PROCESO 2023', 'CONVALIDACION', 'DEUDA DE AÑOS ANTERIORES'
            ]),
        ];
    }
}
