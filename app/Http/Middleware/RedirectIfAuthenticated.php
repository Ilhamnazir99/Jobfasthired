<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect based on role
                $role = Auth::user()->role;

                switch ($role) {
                    case 'admin':
                        return redirect('/admin/dashboard');
                    case 'student':
                        return redirect('/student/dashboard');
                    case 'employer':
                        return redirect('/employer/dashboard');
                    default:
                        return redirect('/'); // Fallback
                }
            }
        }

        return $next($request);
    }
}
