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
        $title = 'Individual Profile Detail';

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
             // Fields for IndividualDetail
            'university' => 'nullable',
            'degree' => 'nullable',
            'year' => 'nullable',
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
                'degree' => $request->degree,
                'year' => $request->year,
            ]
        );
        $individualDetail = IndividualDetail::where('individual_id', $user->id)->first();

        if (!$individualDetail) {
            $individualDetail = new IndividualDetail();
            $individualDetail->individual_id = $user->id; // Assign ID if creating a new record
        }
        // Handle adhar_card file upload
        if ($request->hasFile('adhar_card')) {
            // Delete old file if exists
            Helper::deleteFile($individualDetail->adhar_card);
            
            // Save new file
            $individualDetail->adhar_card = Helper::saveFile($request->file('adhar_card'), 'individuals/adharcard');
            $individualDetail->save();
        }

        return redirect()->back()->with('success', 'Individual Profile updated successfully.');
    }

}