<?php

namespace App\Providers;

use App\Models\Incident;
use App\Policies\IncidentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Incident::class => IncidentPolicy::class,
    ];

    public function boot(): void
    {
        
    }
}
