<?php

namespace App\Http\Controllers\Individual;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\FileCase;
use App\Models\IndividualDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\State;

class FileCaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:individual');
    }

    public function index(Request $request): View | RedirectResponse
    {
        $title = 'File a Case';

        $individual = auth('individual')->user();
        $states = State::all();

        if (!$individual) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        return view('individual.file-case', compact('individual', 'states', 'title'));
    }



    public function registerCase(Request $request)
    {  
        $user = Auth::guard('individual')->user();
    
        // Validation
        $validator = Validator::make($request->all(), [
            'claimant_first_name' => 'required|string|max:100',
            'claimant_middle_name' => 'nullable|string|max:100',
            'claimant_last_name' => 'nullable|string|max:100',
            'claimant_mobile' => 'required|digits:10',
            'claimant_email' => 'required|email|max:100',
            'claimant_address1' => 'required',
            'claimant_address2' => 'nullable',
            'claimant_address_type' => 'required',
            'claimant_state_id' => 'required|exists:states,id',
            'claimant_city_id' => 'required|exists:cities,id',
            'claimant_pincode' => 'required',
            'respondent_first_name' => 'required|string|max:100',
            'respondent_middle_name' => 'nullable|string|max:100',
            'respondent_last_name' => 'nullable|string|max:100',
            'respondent_mobile' => 'required|digits:10',
            'respondent_email' => 'required|email|max:100',
            'respondent_address1' => 'required',
            'respondent_address2' => 'nullable',
            'respondent_address_type' => 'required',
            'respondent_state_id' => 'required|exists:states,id',
            'respondent_city_id' => 'required|exists:cities,id',
            'respondent_pincode' => 'required',
            'add_respondent' => 'nullable|max:4096',
            'brief_of_case' => 'required',
            'amount_in_dispute' => 'nullable',
            'case_type' => 'required',
            'language' => 'nullable',
            'agreement_exist' => 'nullable',
            'upload_evidence' => 'nullable|max:4096',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Initialize variables
        $addRespondentPath = null;
        $uploadEvidencePath = null;
    
        // Handle file uploads
        if ($request->hasFile('add_respondent')) {
            $addRespondentPath = Helper::saveFile($request->file('add_respondent'), 'individuals/casefile');
        }
        
        if ($request->hasFile('upload_evidence')) {
            $uploadEvidencePath = Helper::saveFile($request->file('upload_evidence'), 'individuals/casefile');
        }
    
        // Save case data with proper file paths
        $case = FileCase::create([
            'user_type'                 => 1,
            'individual_id'             => $user->id,
            'claimant_first_name'       => $request->claimant_first_name,
            'claimant_middle_name'      => $request->claimant_middle_name,
            'claimant_last_name'        => $request->claimant_last_name,
            'claimant_mobile'           => $request->claimant_mobile,
            'claimant_email'            => $request->claimant_email,
            'claimant_address1'         => $request->claimant_address1,
            'claimant_address2'         => $request->claimant_address2,
            'claimant_address_type'     => $request->claimant_address_type,
            'claimant_state_id'         => $request->claimant_state_id,
            'claimant_city_id'          => $request->claimant_city_id,
            'claimant_pincode'          => $request->claimant_pincode,
            'respondent_first_name'     => $request->respondent_first_name,
            'respondent_middle_name'    => $request->respondent_middle_name,
            'respondent_last_name'      => $request->respondent_last_name,
            'respondent_mobile'         => $request->respondent_mobile,
            'respondent_email'          => $request->respondent_email,
            'respondent_address1'       => $request->respondent_address1,
            'respondent_address2'       => $request->respondent_address2,
            'respondent_address_type'   => $request->respondent_address_type,
            'respondent_state_id'       => $request->respondent_state_id,
            'respondent_city_id'        => $request->respondent_city_id,
            'respondent_pincode'        => $request->respondent_pincode,
            'brief_of_case'             => $request->brief_of_case,
            'amount_in_dispute'         => $request->amount_in_dispute,
            'case_type'                 => $request->case_type,
            'language'                  => $request->language,
            'agreement_exist'           => $request->agreement_exist,
            'add_respondent'            => $addRespondentPath,
            'upload_evidence'           => $uploadEvidencePath,
        ]);
        
        return response()->json(['success' => true, 'message' => 'Case registered successfully!']);
        // return redirect()->back()->with('success', 'Case registered successfully.');
    }
    

}