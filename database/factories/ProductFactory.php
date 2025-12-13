<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product' => $this->faker->sentence(3),    
            'description'  => $this->faker->paragraph(2),     
        ];
    }
}
