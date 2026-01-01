<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function __construct()
    {
        // Middleware is handled in routes
        // Additional admin check
        if (Auth::check() && !Auth::user()->is_superadmin) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }
    }

    public function index()
    {
        // Redirect to Stripe & Fees page since platform settings are now managed there
        return redirect()->route('admin.stripe-fees.index');
    }

    public function update(Request $request)
    {
        // Redirect to Stripe & Fees page since platform settings are now managed there
        return redirect()->route('admin.stripe-fees.index');
    }
}
