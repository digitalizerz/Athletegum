<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Show the athlete registration form
     */
    public function showRegisterForm()
    {
        return view('athlete.auth.register');
    }

    /**
     * Handle athlete registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:athletes,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $athlete = Athlete::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($athlete));

        Auth::guard('athlete')->login($athlete);

        return redirect()->route('athlete.dashboard');
    }

    /**
     * Show the athlete login form
     */
    public function showLoginForm()
    {
        return view('athlete.auth.login');
    }

    /**
     * Handle athlete login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('athlete')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('athlete.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle athlete logout
     */
    public function logout(Request $request)
    {
        Auth::guard('athlete')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('athlete.login');
    }
}
