<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Show profile setup form (step 2 after registration)
     */
    public function showSetup()
    {
        $athlete = Auth::guard('athlete')->user();
        return view('athlete.profile.setup', compact('athlete'));
    }

    /**
     * Handle profile setup
     */
    public function setup(Request $request)
    {
        $athlete = Auth::guard('athlete')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'athlete_level' => ['required', 'string', 'in:pro,college,highschool'],
            'sport' => ['nullable', 'string', 'max:255'],
            'school' => ['nullable', 'string', 'max:255'],
        ]);

        $athlete->name = $validated['name'];
        $athlete->athlete_level = $validated['athlete_level'];
        $athlete->sport = $validated['sport'];
        $athlete->school = $validated['school'];

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($athlete->profile_photo) {
                Storage::disk('public')->delete($athlete->profile_photo);
            }

            $path = $request->file('profile_photo')->store('athlete-photos', 'public');
            $athlete->profile_photo = $path;
        }

        $athlete->save();

        return redirect()->route('athlete.profile.social');
    }

    /**
     * Show social media setup form (step 3)
     */
    public function showSocial()
    {
        $athlete = Auth::guard('athlete')->user();
        return view('athlete.profile.social', compact('athlete'));
    }

    /**
     * Handle social media setup
     */
    public function updateSocial(Request $request)
    {
        $athlete = Auth::guard('athlete')->user();

        $validated = $request->validate([
            'instagram_handle' => ['nullable', 'string', 'max:255'],
            'tiktok_handle' => ['nullable', 'string', 'max:255'],
            'twitter_handle' => ['nullable', 'string', 'max:255'],
            'youtube_handle' => ['nullable', 'string', 'max:255'],
        ]);

        // Clean handles (remove @ if present)
        $athlete->instagram_handle = $validated['instagram_handle'] ? ltrim($validated['instagram_handle'], '@') : null;
        $athlete->tiktok_handle = $validated['tiktok_handle'] ? ltrim($validated['tiktok_handle'], '@') : null;
        $athlete->twitter_handle = $validated['twitter_handle'] ? ltrim($validated['twitter_handle'], '@') : null;
        $athlete->youtube_handle = $validated['youtube_handle'] ? ltrim($validated['youtube_handle'], '@') : null;

        $athlete->save();

        return redirect()->route('athlete.profile.preview');
    }

    /**
     * Show profile preview and share link (step 4)
     */
    public function showPreview()
    {
        $athlete = Auth::guard('athlete')->user();
        return view('athlete.profile.preview', compact('athlete'));
    }

    /**
     * Show public athlete profile (link-only access)
     */
    public function showPublic($identifier)
    {
        // Try to find by username first, then by token
        $athlete = Athlete::where('username', $identifier)
            ->orWhere('profile_token', $identifier)
            ->firstOrFail();

        try {
            $completedDeals = $athlete->completedDeals()->get();
            $businesses = $athlete->businessesWorkedWith();
            $dealTypes = $athlete->completed_deal_types;
        } catch (\Exception $e) {
            // If there's an error, set defaults
            $completedDeals = collect();
            $businesses = collect();
            $dealTypes = [];
        }

        return view('athlete.profile.public', compact('athlete', 'completedDeals', 'businesses', 'dealTypes'));
    }

    /**
     * Show profile edit form (from dashboard)
     */
    public function edit()
    {
        $athlete = Auth::guard('athlete')->user();
        return view('athlete.profile.edit', compact('athlete'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $athlete = Auth::guard('athlete')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'athlete_level' => ['required', 'string', 'in:pro,college,highschool'],
            'sport' => ['nullable', 'string', 'max:255'],
            'school' => ['nullable', 'string', 'max:255'],
            'instagram_handle' => ['nullable', 'string', 'max:255'],
            'tiktok_handle' => ['nullable', 'string', 'max:255'],
            'twitter_handle' => ['nullable', 'string', 'max:255'],
            'youtube_handle' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:athletes,username,' . $athlete->id],
        ]);

        $athlete->name = $validated['name'];
        $athlete->athlete_level = $validated['athlete_level'];
        $athlete->sport = $validated['sport'];
        $athlete->school = $validated['school'];
        $athlete->username = $validated['username'] ?? null;

        // Clean social handles
        $athlete->instagram_handle = $validated['instagram_handle'] ? ltrim($validated['instagram_handle'], '@') : null;
        $athlete->tiktok_handle = $validated['tiktok_handle'] ? ltrim($validated['tiktok_handle'], '@') : null;
        $athlete->twitter_handle = $validated['twitter_handle'] ? ltrim($validated['twitter_handle'], '@') : null;
        $athlete->youtube_handle = $validated['youtube_handle'] ? ltrim($validated['youtube_handle'], '@') : null;

        if ($request->hasFile('profile_photo')) {
            if ($athlete->profile_photo) {
                Storage::disk('public')->delete($athlete->profile_photo);
            }
            $path = $request->file('profile_photo')->store('athlete-photos', 'public');
            $athlete->profile_photo = $path;
        }

        $athlete->save();

        return redirect()->route('athlete.dashboard')->with('success', 'Profile updated successfully.');
    }
}
