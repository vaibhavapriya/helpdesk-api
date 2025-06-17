<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Ticket;
use App\Policies\TicketPolicy;
use App\Models\Profile;
use App\Policies\ProfilePolicy;
use App\Models\User;
use App\Policies\UserPolicy;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        //Passport::routes(); 
        Gate::policy(Ticket::class,TicketPolicy::class);
        Gate::policy(Profile::class,ProfilePolicy::class);
        Gate::policy(User::class,UserPolicy::class);
        
    }
    
}
