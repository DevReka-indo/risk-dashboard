<?php

namespace Database\Factories;

use App\Models\Risk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Risk>
 */
class RiskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $level = fake()->randomElement(['Low', 'Medium', 'High', 'Critical']);

        $nilai = match ($level) {
            'Low' => fake()->numberBetween(1, 5),
            'Medium' => fake()->numberBetween(6, 12),
            'High' => fake()->numberBetween(13, 20),
            'Critical' => fake()->numberBetween(21, 25),
        };

        return [
            'name' => fake()->sentence(3, false),
            'description' => fake()->paragraph(),
            'department' => fake()->randomElement(['IT', 'Finance', 'HR', 'Operations', 'Marketing', 'Legal']),
            'category' => fake()->randomElement(['Operational', 'Strategic', 'Financial', 'Compliance', 'Reputational']),
            'level' => $level,
            'nilai' => $nilai,
            'status' => fake()->randomElement(['Open', 'In Progress', 'Closed']),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Open',
        ]);
    }

    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 'High',
            'nilai' => fake()->numberBetween(13, 20),
        ]);
    }

    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'level' => 'Critical',
            'nilai' => fake()->numberBetween(21, 25),
        ]);
    }
}
