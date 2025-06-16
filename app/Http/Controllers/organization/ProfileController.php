<?php

namespace App\Http\Controllers\Organization;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\OrganizationDetail;
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
        $this->middleware('auth:organization');
    }

    public function index(Request $request): View | RedirectResponse
    {
        $title = 'Organization Profile Detail';

        $organization = auth('organization')->user();
        $states = State::all();

        if (!$organization) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        $organizationDetail = $organization->organizationDetail ?? null;

        return view('organization.profile', compact('organization','states','title','organizationDetail'));
    }

    public function update(Request $request)
    {
        $user = Auth::guard('organization')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:organizations,email,' . $user->id,
            'mobile' => 'required|digits:10',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'image' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'email_secondary' => 'nullable',
            'mobile_secondary' => 'nullable',
            'pincode' => 'nullable',
            'address1' => 'nullable',
            'address2' => 'nullable',
             // Fields for OrganizationDetail
             'organization_type' => 'nullable',
             'description' => 'nullable',
             'registration_no' => 'nullable',
             'registration_certificate' => 'nullable',
             'attach_registration_certificate' => 'nullable',
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
        $user->email_secondary = $request->email_secondary;
        $user->mobile_secondary = $request->mobile_secondary;
        $user->pincode = $request->pincode;
        $user->address1 = $request->address1;
        $user->address2 = $request->address2;

        // Handle image upload
        if ($request->hasFile('image')) {
            Helper::deleteFile($user->image); // Delete old image
            $user->image = Helper::saveFile($request->file('image'), 'organizations');
        }

        $user->save();
        
        // Update or create OrganizationDetail
        $organizationDetail = OrganizationDetail::updateOrCreate(
            ['organization_id' => $user->id], // Find by this column
            [
                'organization_type' => $request->organization_type,
                'description' => $request->description,
                'registration_no' => $request->registration_no,
                'registration_certificate' => $request->registration_certificate,
                'attach_registration_certificate' => $request->attach_registration_certificate,
            ]
        );
        $organizationDetail = OrganizationDetail::where('organization_id', $user->id)->first();

        if (!$organizationDetail) {
            $organizationDetail = new OrganizationDetail();
            $organizationDetail->individual_id = $user->id; // Assign ID if creating a new record
        }
        // Handle attach_registration_certificate file upload
        if ($request->hasFile('attach_registration_certificate')) {
            // Delete old file if exists
            Helper::deleteFile($organizationDetail->attach_registration_certificate);
            
            // Save new file
            $organizationDetail->attach_registration_certificate = Helper::saveFile($request->file('attach_registration_certificate'), 'organization/registrationcertificate');
            $organizationDetail->save();
        }

        return redirect()->back()->with('success', 'Organization Profile updated successfully.');
    }
}