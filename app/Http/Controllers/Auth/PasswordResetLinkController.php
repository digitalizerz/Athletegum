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
        return view('auth.forgot-password', [
            'routeName' => 'password.email',
        ]);
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

        // Use default users broker (businesses are stored in users table)
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
            
            \Log::info('Password reset link sent (business)', [
                'email' => $request->email,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            \Log::error('Password reset email failed (business)', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Always return success message (security: prevent user enumeration)
        return back()->with('status', 'If an account exists for this email, we\'ve sent a password reset link.');
    }
}
