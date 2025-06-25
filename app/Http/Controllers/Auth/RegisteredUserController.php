<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:student,employer,admin'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(RouteServiceProvider::redirectToRoleBasedDashboard());
    }

    /**
     * Show the dedicated student registration form.
     */
    public function showStudentRegisterForm(): View
    {
        if (auth()->check()) {
            return redirect(RouteServiceProvider::redirectToRoleBasedDashboard());
        }

        return view('auth.register-student');
    }

    /**
     * Handle student registration submission.
     */
    public function registerStudent(Request $request): RedirectResponse
    {
        // ✅ Normalize phone number first
        $normalizedPhone = '+60' . ltrim($request->phone_number, '0');
        $request->merge(['phone_number' => $normalizedPhone]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'phone_number' => 'required|string|max:20|unique:users',
            'address' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'phone_number' => $normalizedPhone,
            'address' => $request->address,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('student.dashboard');
    }

    /**
     * Show the employer registration form.
     */
    public function showEmployerRegisterForm(): View
    {
        if (auth()->check()) {
            return redirect(RouteServiceProvider::redirectToRoleBasedDashboard());
        }

        return view('auth.register-employer');
    }

    /**
     * Handle employer registration submission.
     */
    public function registerEmployer(Request $request): RedirectResponse
    {
        // ✅ Normalize phone number first
        $normalizedPhone = '+60' . ltrim($request->phone_number, '0');
        $request->merge(['phone_number' => $normalizedPhone]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'company_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:users',
            'address' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'company_name' => $request->company_name,
            'phone_number' => $normalizedPhone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => 'employer',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('employer.dashboard');
    }
}
