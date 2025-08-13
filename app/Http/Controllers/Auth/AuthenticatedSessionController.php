<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    
    public function create(): View
    {
        return view('auth.login');
    }

    
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user && $user->role === 'utilisateur' && RouteFacade::has('utilisateur.home')) {
            return redirect()->intended(route('utilisateur.home'));
        }
        if ($user && $user->role === 'resolveur' && RouteFacade::has('resolveur.incidents.index')) {
            return redirect()->intended(route('resolveur.incidents.index'));
        }
        if ($user && $user->role === 'admin' && RouteFacade::has('admin.dashboard')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if (RouteFacade::has('dashboard')) {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->intended('/');
    }

   
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
