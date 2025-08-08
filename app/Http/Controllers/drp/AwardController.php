<?php

namespace App\Http\Controllers\Drp;

use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Models\Award;
use App\Models\AwardVariable;
use App\Models\Drp;
use Illuminate\View\View;
use Illuminate\Support\Str;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        $drp = auth('drp')->user();
        $title = "Award";
        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 1) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }

        if (!$request->ajax()) {
            ################## Profile Incomplete Start ##################
            $drpdata = Drp::with('drpDetail')->where('id', $drp->id)->first();

            // Required fields to check
            $requiredFields = [
                'name', 'email', 'mobile', 'state_id', 'city_id', 'pincode', 'image',
                'signature_drp', 'dob', 'nationality', 'gender', 'address1', 'profession', 'specialization',
            ];

            // Check if any field is null or empty
            $missingFields = collect($requiredFields)->filter(fn($field) => empty($drpdata->$field));

            if ($missingFields->isNotEmpty()) {
                return view('drp.award.index', compact('drpdata', 'title'))
                    ->with('showProfilePopup', true);
            }
            ################## Profile Incomplete End ##################
        }
        
        if ($request->ajax()) {
            $data = Award::select('id', 'name', 'status', 'created_at');

            return Datatables::of($data)
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1
                        ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>'
                        : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    
                    // if (Helper::userCan(111, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('drp.award.edit', $row['id']) . '">Edit</a>';
                    // }
                    // if (Helper::userCan(111, 'can_delete')) {
                        // $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    // }
                    return $btn;
                    // return Helper::userAllowed(111) ? $btn : '';
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('drp.award.index', compact('title'));
    }


    public function add(): View|RedirectResponse
    {
        $drp = auth('drp')->user();
        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 1) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }
    
        $title = "Add Award";
        return view('drp.award.add', compact('title'));
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'unique:awards,name', 'max:50'],
            'subject'        => ['required', 'string'],
            'email_content'  => ['required', 'string'],
            'notice_format'  => ['required', 'string']
        ]);

        Award::create($validated + ['status' => 1]);
        
        return to_route('drp.award')->withSuccess('Award Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $drp = auth('drp')->user();
        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 1) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }
    
        $title = "Add Award";
        $award = Award::find($id);
        if (!$award) return to_route('drp.award')->withError('Award Not Found..!!');

        return view('drp.award.edit', compact('award','title'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $award = Award::find($id);
        if (!$award) return to_route('drp.award')->withError('Award Not Found..!!');

        $validated = $request->validate([
            'name'           => ['required', 'string', 'unique:awards,name,' . $id, 'max:50'],
            'subject'        => ['required', 'string'],
            'email_content'  => ['required', 'string'],
            'notice_format'  => ['required', 'string']
        ]);

        $award->update($validated);
        
        return to_route('drp.award')->withSuccess('Award Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Award, $request->id);
    }

    public function getawardVariables(): JsonResponse
    {
        return response()->json(AwardVariable::all());
    }

}
