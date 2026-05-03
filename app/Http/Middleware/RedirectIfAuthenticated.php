<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                
                if ($user->role == 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role == 'petugas') {
                    return redirect()->route('petugas.dashboard');
                } elseif ($user->role == 'mahasiswa') {
                    return redirect()->route('mahasiswa.dashboard');
                }
                
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}