<?php

namespace App\Http\Controllers\Individual;

use App\Http\Controllers\Controller;
use App\Models\Individual;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:individual');
    }

    public function index(Request $request): View | RedirectResponse
    {
        $title = 'Individual Dashboard';
        $individual = auth('individual')->user();
        $individualId = $individual ? $individual->individualId : null;
        $individualSlug = $individual?->slug;
    
        $indivuduals = Individual::with('individualDetail')->where('slug', $individualSlug)->first();
    
        if (!$indivuduals) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
        
        // Required fields to check
        $requiredFields = [
            'name', 'email', 'mobile', 'state_id', 'city_id', 'image', 
            'last_name', 'dob', 'nationality', 'gender', 'pincode', 
            'father_name', 'address1'
        ];

        $requiredDetailFields = ['university', 'degree', 'year'];

        // Check if any field is null or empty
        $missingFields = collect($requiredFields)->filter(fn($field) => empty($indivuduals->$field));
        $missingDetailFields = collect($requiredDetailFields)->filter(fn($field) => empty($indivuduals->individualDetail?->$field));

        if ($missingFields->isNotEmpty() || $missingDetailFields->isNotEmpty()) {
            return view('individual.dashboard', compact('indivuduals', 'title'))
                ->with('showProfilePopup', true);
        }

        return view('individual.dashboard', compact('indivuduals', 'title'));
    }

}
