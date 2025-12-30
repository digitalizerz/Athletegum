<?php

namespace App\Http\Controllers\Athlete\Auth;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', [
            'request' => $request,
            'routeName' => 'athlete.password.store',
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        try {
            $status = Password::broker('athletes')->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (Athlete $athlete) use ($request) {
                    $athlete->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($athlete));
                }
            );

            // Log the status for debugging
            \Log::info('Password reset attempt (athlete)', [
                'email' => $request->email,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Password reset failed (athlete)', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'An error occurred while resetting your password. Please try again.']);
        }

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('athlete.login')->with('status', 'Your password has been reset successfully.')
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
