<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Image;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fn () => User::factory(),
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            //'image_id' => Image::factory(),
            'phone' => fake()->phoneNumber(),
            'email' => fn (array $attributes) => User::find($attributes['user_id'])->email,
        ];
    }
}
