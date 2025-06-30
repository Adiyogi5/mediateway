<?php

namespace App\Http\Controllers\Drp;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\DrpDetail;
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
        $this->middleware('auth:drp');
    }

    public function index(Request $request): View | RedirectResponse
    {
        $title = 'Profile Detail';

        $drp = auth('drp')->user();
        $states = State::all();

        if (!$drp) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        $drpDetail = $drp->drpDetail;

        return view('drp.profile', compact('drp','states','title','drpDetail'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('drp')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:drps,email,' . $user->id,
            'mobile' => 'required|digits:10',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'signature_drp' => 'nullable|mimes:jpg,jpeg,png|max:2048',
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
            // Fields for DrpDetail
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
            $user->image = Helper::saveFile($request->file('image'), 'drps');
        }
        if ($request->hasFile('signature_drp')) {
            Helper::deleteFile($user->signature_drp); // Delete old image
            $user->signature_drp = Helper::saveFile($request->file('signature_drp'), 'drps');
        }

        $user->save();


        // Handle file upload first
        $attachFilePath = null;
        if ($request->hasFile('attach_registration_certificate')) {
            $existingDetail = DrpDetail::where('drp_id', $user->id)->first();

            if ($existingDetail && $existingDetail->attach_registration_certificate) {
                Helper::deleteFile($existingDetail->attach_registration_certificate);
            }

            $attachFilePath = Helper::saveFile($request->file('attach_registration_certificate'), 'drp/registrationcertificate');
        }

        // Prepare detail data
        $detailData = [
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
            'experience_in_the_field_of_drp' => $request->experience_in_the_field_of_drp,
            'areas_of_expertise' => $request->areas_of_expertise,
            'membership_of_professional_organisation' => $request->membership_of_professional_organisation,
            'no_of_awards_as_arbitrator' => $request->no_of_awards_as_arbitrator,
            'total_years_of_working_as_drp' => $request->total_years_of_working_as_drp,
            'functional_area_of_drp' => $request->functional_area_of_drp,
        ];

        // Add the file path only if a new file was uploaded
        if ($attachFilePath) {
            $detailData['attach_registration_certificate'] = $attachFilePath;
        }

        DrpDetail::updateOrCreate(
            ['drp_id' => $user->id],
            $detailData
        );

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}