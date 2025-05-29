<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\ClaimPetition;
use App\Models\ClaimPetitionVariable;
use Illuminate\View\View;
use Illuminate\Support\Str;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClaimPetitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
{
    if ($request->ajax()) {
        $data = ClaimPetition::select('id', 'product_type', 'name', 'status', 'created_at');

        return Datatables::of($data)
            ->editColumn('product_type', function ($row) {
                $drpTypes = config('constant.product_type');
                return $drpTypes[$row->product_type] ?? 'Unknown';
            })
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
                
                if (Helper::userCan(111, 'can_edit')) {
                    $btn .= '<a class="dropdown-item" href="' . route('claimpetition.edit', $row['id']) . '">Edit</a>';
                }
                // if (Helper::userCan(111, 'can_delete')) {
                //     $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                // }

                return Helper::userAllowed(111) ? $btn : '';
            })
            ->orderColumn('created_at', function ($query, $order) {
                $query->orderBy('created_at', $order);
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    $title = "Claim Petition";
    return view('claimpetition.index', compact('title'));
}


public function add(): View
{
    return view('claimpetition.add');
}

public function save(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'product_type'       => ['required', 'integer'],
        'name'           => ['required', 'string', 'unique:order_sheets,name', 'max:100'],
        'subject'        => ['required', 'string'],
        'email_content'  => ['required', 'string'],
        'notice_format'  => ['required', 'string']
    ]);

    ClaimPetition::create($validated + ['status' => 1]);
    
    return to_route('claimpetition')->withSuccess('Claim Petition Added Successfully..!!');
}

public function edit($id): View|RedirectResponse
{
    $claimpetition = ClaimPetition::find($id);
    if (!$claimpetition) return to_route('claimpetition')->withError('Claim Petition Not Found..!!');

    return view('claimpetition.edit', compact('claimpetition'));
}

public function update(Request $request, $id): RedirectResponse
{
    $claimpetition = ClaimPetition::find($id);
    if (!$claimpetition) return to_route('claimpetition')->withError('Claim Petition Not Found..!!');

    $validated = $request->validate([
        'product_type'       => ['required', 'integer'],
        'name'           => ['required', 'string', 'unique:order_sheets,name,' . $id, 'max:100'],
        'subject'        => ['required', 'string'],
        'email_content'  => ['required', 'string'],
        'notice_format'  => ['required', 'string']
    ]);

    $claimpetition->update($validated);
    
    return to_route('claimpetition')->withSuccess('Claim Petition Updated Successfully..!!');
}

public function delete(Request $request): JsonResponse
{
    return Helper::deleteRecord(new ClaimPetition, $request->id);
}

public function getclaimpetitionVariables(): JsonResponse
{
    return response()->json(ClaimPetitionVariable::all());
}

}
