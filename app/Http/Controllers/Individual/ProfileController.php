<?php

namespace App\Http\Controllers\Individual;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\IndividualDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\State;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:individual');
    }

    public function index(Request $request): View | RedirectResponse
    {
        $title = 'Profile Detail';

        $individual = auth('individual')->user();
        $states = State::all();

        if (!$individual) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        $individualDetail = $individual->individualDetail;

        return view('individual.profile', compact('individual', 'states', 'title', 'individualDetail'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('individual')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:individuals,email,' . $user->id,
            'mobile' => 'required|digits:10',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'middle_name' => 'nullable',
            'last_name' => 'nullable',
            'dob' => 'nullable',
            'nationality' => 'nullable',
            'gender' => 'nullable',
            'email_secondary' => 'nullable',
            'mobile_secondary' => 'nullable',
            'pincode' => 'nullable',
            'father_name' => 'nullable',
            'address1' => 'nullable',
            'address2' => 'nullable',
            'profession' => 'nullable',
            'specialization' => 'nullable',
             // Fields for IndividualDetail
            'university' => 'nullable',
            'field_of_study' => 'nullable',
            'degree' => 'nullable',
            'year' => 'nullable',
            'description' => 'nullable',
            'achievement_od_socities' => 'nullable',
            'designation' => 'nullable',
            'organization' => 'nullable',
            'professional_degree' => 'nullable',
            'registration_no' => 'nullable',
            'job_description' => 'nullable',
            'currently_working_here' => 'nullable',
            'years_of_experience' => 'nullable',
            'registration_certificate' => 'nullable',
            'attach_registration_certificate' => 'nullable',
            'experience_in_the_field_of_drp' => 'nullable',
            'areas_of_expertise' => 'nullable',
            'membership_of_professional_organisation' => 'nullable',
            'no_of_awards_as_arbitrator' => 'nullable',
            'total_years_of_working_as_drp' => 'nullable',
            'functional_area_of_drp' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->state_id = $request->state_id;
        $user->city_id = $request->city_id;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->dob = $request->dob;
        $user->nationality = $request->nationality;
        $user->gender = $request->gender;
        $user->email_secondary = $request->email_secondary;
        $user->mobile_secondary = $request->mobile_secondary;
        $user->pincode = $request->pincode;
        $user->father_name = $request->father_name;
        $user->address1 = $request->address1;
        $user->address2 = $request->address2;
        $user->profession = $request->profession;
        $user->specialization = $request->specialization;

        // Handle image upload
        if ($request->hasFile('image')) {
            Helper::deleteFile($user->image); // Delete old image
            $user->image = Helper::saveFile($request->file('image'), 'individuals');
        }

        $user->save();

        // Update or create IndividualDetail
        $individualDetail = IndividualDetail::updateOrCreate(
            ['individual_id' => $user->id], // Find by this column
            [
                'university' => $request->university,
                'field_of_study' => $request->field_of_study,
                'degree' => $request->degree,
                'year' => $request->year,
                'description' => $request->description,
                'achievement_od_socities' => $request->achievement_od_socities,
                'designation' => $request->designation,
                'organization' => $request->organization,
                'professional_degree' => $request->professional_degree,
                'registration_no' => $request->registration_no,
                'job_description' => $request->job_description,
                'currently_working_here' => $request->currently_working_here,
                'years_of_experience' => $request->years_of_experience,
                'registration_certificate' => $request->registration_certificate,
                'attach_registration_certificate' => $request->attach_registration_certificate,
                'experience_in_the_field_of_drp' => $request->experience_in_the_field_of_drp,
                'areas_of_expertise' => $request->areas_of_expertise,
                'membership_of_professional_organisation' => $request->membership_of_professional_organisation,
                'no_of_awards_as_arbitrator' => $request->no_of_awards_as_arbitrator,
                'total_years_of_working_as_drp' => $request->total_years_of_working_as_drp,
                'functional_area_of_drp' => $request->functional_area_of_drp,
            ]
        );
        $individualDetail = IndividualDetail::where('individual_id', $user->id)->first();

        if (!$individualDetail) {
            $individualDetail = new IndividualDetail();
            $individualDetail->individual_id = $user->id; // Assign ID if creating a new record
        }
        // Handle attach_registration_certificate file upload
        if ($request->hasFile('attach_registration_certificate')) {
            // Delete old file if exists
            Helper::deleteFile($individualDetail->attach_registration_certificate);
            
            // Save new file
            $individualDetail->attach_registration_certificate = Helper::saveFile($request->file('attach_registration_certificate'), 'individuals/certificates');
            $individualDetail->save();
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

}