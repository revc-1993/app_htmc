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
            'contract_object' => $this->faker->sentence(3),
            'requesting_area' => $this->faker->sentence(3),
            'reception_date' => $this->faker->date('Y-m-d', 'now'),
            'amount' => $this->faker->randomFloat($maxDecimals = 2, $min = 500, $max = 10000),
            // Posiblemente FK
            'management_status' => $this->faker->randomElement(['Pendiente de revisión', 'En revisión', 'Certificado', 'Observado', 'Devuelto']),
        ];
    }
}
