<?php

namespace App\Http\Controllers\Athlete\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Use the athletes broker to query the athletes table
        // Wrap in try-catch to prevent timeouts from blocking the request
        try {
            Password::broker('athletes')->sendResetLink(
                $request->only('email')
            );
        } catch (\Exception $e) {
            // Log the error but don't expose it to the user
            \Log::error('Password reset email failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);
        }

        // Security: Always return the same success message to prevent user enumeration
        // Never reveal whether an account exists or not
        return back()->with('status', 'If an account exists for this email, we\'ve sent a password reset link.');
    }
}
