<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        // Only update fields that were actually submitted and validated
        $validated = $request->validated();
        foreach ($validated as $key => $value) {
            $user->$key = $value;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Determine which tab was updated based on submitted fields
        // Check for business fields (excluding name and email which are in profile tab)
        $hasBusinessFields = $request->hasAny(['business_name', 'business_information', 'owner_principal', 'phone']);
        $hasProfileFields = $request->has('name') || $request->has('email');
        $hasAddressFields = $request->hasAny(['address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country']);
        
        $status = 'profile-updated';
        if ($hasBusinessFields && !$hasProfileFields) {
            $status = 'business-updated';
        } elseif ($hasAddressFields && !$hasProfileFields && !$hasBusinessFields) {
            $status = 'address-updated';
        }

        // Redirect to appropriate route based on user type
        if ($user->is_superadmin) {
            return Redirect::route('admin.profile.edit')->with('status', $status);
        }
        
        return Redirect::route('profile.edit')->with('status', $status);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
