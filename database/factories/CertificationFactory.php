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
            'certification_memo' => $this->faker->word(10),
            'content' => $this->faker->word(),
            'contract_object' => $this->faker->word(),
            'process_type_id' => $this->faker->randomElement([1, 2, 3]),
            'expense_type_id' => $this->faker->randomElement([1, 2, 3]),
            'department_id' => $this->faker->randomElement([1, 2, 3]),
            // 'process_type' => $this->faker->randomElement([1, 2, 3, 4]),
            'cgf_date' => now(),

        ];
    }
}
