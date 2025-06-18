<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required'], // Capture the role from the login form
        ]);

        $credentials = $request->only('email', 'password');

        // Get the user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and role matches
        if (!$user || $user->role !== $request->role) {
            return back()->withErrors([
                'email' => 'This account does not belong to a ' . ucfirst($request->role) . '.',
            ]);
        }

        // Attempt to login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'employer') {
                return redirect()->route('employer.dashboard');
            } else {
                return redirect()->route('student.dashboard');
            }
        }

        // Wrong password
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
