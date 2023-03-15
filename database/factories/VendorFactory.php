<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'PROVEEDOR A', 'PROVEEDOR B', 'PROVEEDOR C'
            ]),
            'nit' => $this->faker->unique()->randomElement([
                '0912345678', '0987654321', '0900111222'
            ]),
        ];
    }
}
