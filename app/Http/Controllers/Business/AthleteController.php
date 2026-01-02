<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Support\PlanFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AthleteController extends Controller
{
    /**
     * Browse athletes (discovery page)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Athlete::query();
        
        // Feature gating
        $canSearch = PlanFeatures::canUseFeature($user, 'athlete_search');
        $canFilter = PlanFeatures::canUseFeature($user, 'athlete_filters');
        
        // Search (Pro/Growth only)
        if ($canSearch && $request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filters (Pro/Growth only)
        if ($canFilter) {
            if ($request->filled('sport')) {
                $query->where('sport', $request->sport);
            }
            
            if ($request->filled('school')) {
                $query->where('school', 'like', "%{$request->school}%");
            }
        }
        
        // Get unique sports and schools for filter dropdowns (only if user can filter)
        $sports = [];
        $schools = [];
        if ($canFilter) {
            $sports = Athlete::whereNotNull('sport')
                ->where('sport', '!=', '')
                ->distinct()
                ->pluck('sport')
                ->sort()
                ->values()
                ->toArray();
            
            $schools = Athlete::whereNotNull('school')
                ->where('school', '!=', '')
                ->distinct()
                ->pluck('school')
                ->sort()
                ->values()
                ->toArray();
        }
        
        // Limit results for free users
        if (!$canSearch) {
            // Free users: limit to first 12 athletes
            $athletes = $query->orderBy('created_at', 'desc')->limit(12)->get();
            $totalCount = $athletes->count();
            $perPage = 12;
            $currentPage = 1;
        } else {
            // Pro/Growth: full pagination
            $perPage = 12;
            $athletes = $query->orderBy('created_at', 'desc')->paginate($perPage);
            $totalCount = $athletes->total();
            $currentPage = $athletes->currentPage();
        }
        
        return view('business.athletes.index', [
            'athletes' => $athletes,
            'sports' => $sports,
            'schools' => $schools,
            'canSearch' => $canSearch,
            'canFilter' => $canFilter,
            'totalCount' => $totalCount,
            'currentPage' => $currentPage,
            'filters' => [
                'search' => $request->search ?? '',
                'sport' => $request->sport ?? '',
                'school' => $request->school ?? '',
            ],
        ]);
    }
    
    /**
     * Show athlete profile (read-only for now)
     */
    public function show(Athlete $athlete)
    {
        return view('business.athletes.show', [
            'athlete' => $athlete,
        ]);
    }
}

