<?php

namespace App\Http\Controllers\Auth;

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

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
            
            // Log the status for debugging
            \Log::info('Password reset link sent (business)', [
                'email' => $request->email,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't expose it to the user
            \Log::error('Password reset email failed (business)', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Return success to prevent user enumeration
            return back()->with('status', 'If an account exists for this email, we\'ve sent a password reset link.');
        }

        // Security: Always return the same success message to prevent user enumeration
        // Never reveal whether an account exists or not
        return back()->with('status', 'If an account exists for this email, we\'ve sent a password reset link.');
    }
}
