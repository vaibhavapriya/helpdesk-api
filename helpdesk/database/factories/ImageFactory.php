<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filetype = fake()->randomElement(['pdf', 'jpg', 'png']);
        $filename = fake()->lexify('file_????') . '.' . $filetype;

        return [
            'filetype' => $filetype,
            'name' => $filename,
            'link' => 'https://example.com/files/' . $filename,
            //'size' => fake()->numberBetween(100, 5000), // size in KB
        ];
    }
}
