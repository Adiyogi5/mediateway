<?php

namespace App\Http\Controllers\Organization;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FileCaseImport;
use App\Exports\SampleFileCaseExport;
use App\Models\FileCase;
use App\Models\State;
use Illuminate\Http\RedirectResponse;

class FileCaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organization');
    }

    public function index(Request $request): View|JsonResponse
    {
        $title = 'File Cases List';

        $organization = auth('organization')->user();

        if (!$organization) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        if ($request->ajax()) {
            $data = FileCase::select('file_cases.id', 'file_cases.case_type', 'file_cases.organization_id', 'file_cases.claimant_first_name', 'file_cases.claimant_last_name', 'file_cases.claimant_mobile', 'file_cases.respondent_first_name', 'file_cases.respondent_last_name', 'file_cases.respondent_mobile', 'file_cases.status', 'file_cases.created_at')
                ->where('file_cases.organization_id', auth()->id())
                ->where('file_cases.status', 1);
            return Datatables::of($data)
                ->editColumn('case_type', function ($row) {
                    return config('constant.case_type')[$row->case_type] ?? 'Unknown';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    // if (Helper::userCan(103, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('organization.cases.filecaseview.edit', $row->id) . '">Edit</a>';
                    // }
                    // if (Helper::userCan(103, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    // }

                    // if (Helper::userAllowed(103)) {
                        return $btn;
                    // } else {
                    //     return '';
                    // }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'status','case_type'])
                ->make(true);
        }

        return view('organization.cases.filecaseview', compact('organization','title'));
    }

    public function edit($id): View|RedirectResponse
    {
        $title = 'Edit Filed Case';
        $organization_authData = auth('organization')->user();
        
        $caseviewData   = FileCase::Find($id);
        $states = State::all();
        
        if (!$caseviewData) {
            return to_route('organization.cases.filecaseview')->withError('Filed Case Not Found..!!');
        }
        return view('organization.cases.edit', compact('caseviewData','title','organization_authData','states'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        // dd($request->all());
        $caseviewData   = FileCase::Find($id);
        $organization_authData = auth('organization')->user();

        if (!$caseviewData) {
            return to_route('organization.cases.filecaseview')->withError('Filed Case Not Found..!!');
        }

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

        // Handle file uploads only if new files are uploaded
        if ($request->hasFile('add_respondent')) {
            $addRespondentPath = Helper::saveFile($request->file('add_respondent'), 'organization/casefile');
        } else {
            $addRespondentPath = $caseviewData->add_respondent; // Keep old file if no new file is uploaded
        }

        if ($request->hasFile('upload_evidence')) {
            $uploadEvidencePath = Helper::saveFile($request->file('upload_evidence'), 'organization/casefile');
        } else {
            $uploadEvidencePath = $caseviewData->upload_evidence; // Keep old file if no new file is uploaded
        }

        // Update case data
        $caseviewData->update([
            'usertype'                  => 2,
            'organization_id'           => $organization_authData->id,
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

        return to_route('organization.cases.filecaseview')->withSuccess('Filed Case Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new FileCase, $request->id);
    }

    public function filecase(Request $request): View
    {
        $title = 'File Cases';

        $organization = auth('organization')->user();

        if (!$organization) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        return view('organization.cases.filecase', compact('organization','title'));
    }

        public function importExcel(Request $request)
        {
            $request->validate([
                'file' => 'required|mimes:xlsx,csv',
            ]);

            $organizationId = auth()->user()->id ?? null; // Adjust based on your logic

            if (!$organizationId) {
                return back()->with('error', 'No organization found.');
            }
    
            Excel::import(new FileCaseImport($organizationId), $request->file('file'));
    
            return back()->with('success', 'File imported successfully.');
        }


    public function downloadSample()
    {
        return Excel::download(new SampleFileCaseExport, 'sample_file_case.xlsx');
    }

}