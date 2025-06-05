<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Image;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_id' =>User::factory(),
            'title' => fake()->words(3, true),//->sentence(),
            'description' => fake()->sentence(),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            //'image_id' => Image::factory(),
            'status' => fake()->randomElement(['open', 'closed']),
            'department' => fake()->word(),
            // 'requester' => $this->faker->name,'user_id' => User::factory(), 
            //'last_reply' => fake()->dateTimeBetween('-1 week', 'now'),
            //'last_replier' => fake()->optional()->dateTime
        ];
    }
}
