<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certification>
 */
class CertificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'certification_memo' => $this->faker->word(),
            'content' => $this->faker->word(3),
            'process_type' => $this->faker->randomElement(["PROCESO 1", "PROCESO 2", "PROCESO 3"]),
            // 'process_type' => $this->faker->randomElement([1, 2, 3, 4]),
            'contract_object' => $this->faker->word(3),
            'expense_type' => $this->faker->randomElement(["CONVALIDACIÓN", "DEUDA", "PROCESO 2023"]),
            'cgf_date' => now(),
        ];
    }
}
