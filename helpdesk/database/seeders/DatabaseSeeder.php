<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Ticket;
use App\Models\Reply;
use App\Models\Image;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

public function run(): void
{
    User::factory()->count(10)->create()->each(function ($user) {
        
        // Create a profile for each user
        $profile = Profile::factory()->for($user)->create();

        // Create image for the profile (polymorphic)
        Image::factory()->create([
            'imageable_id' => $profile->id,
            'imageable_type' => Profile::class,
        ]);

        // Create 10 tickets for each user
        Ticket::factory()->count(10)->for($user, 'requester')->create()->each(function ($ticket) {
            
            // Create image for each ticket (polymorphic)
            Image::factory()->create([
                'imageable_id' => $ticket->id,
                'imageable_type' => Ticket::class,
            ]);

            // Create between 3 and 4 replies per ticket
            $count = rand(3, 4);
            Reply::factory()->count($count)->for($ticket)->create();
        });
    });
}
}
