<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; 

class AppServiceProvider extends ServiceProvider
{
   
    public function register(): void
    {
        
    }

  public function boot(): void
{
    Schema::defaultStringLength(191); 

    \Illuminate\Support\Facades\Redirect::macro('intended', function ($default = null) {
        $user = auth()->user();

        if ($user?->role === 'admin') {
            return redirect()->route('admin.incidents.index');
        }

        if ($user?->role === 'utilisateur') {
            return redirect()->route('utilisateur.home');
        }

        return redirect($default ?? 'utilisateur/home');
    });
}

}
