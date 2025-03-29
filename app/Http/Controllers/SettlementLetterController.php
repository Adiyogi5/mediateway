<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\SettlementLetter;
use App\Models\SettlementLetterVariable;
use Illuminate\View\View;
use Illuminate\Support\Str;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettlementLetterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
{
    if ($request->ajax()) {
        $data = SettlementLetter::select('id', 'drp_type', 'name', 'status', 'created_at');

        return Datatables::of($data)
            ->editColumn('drp_type', function ($row) {
                $drpTypes = config('constant.drp_type');
                return $drpTypes[$row->drp_type] ?? 'Unknown';
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
                    $btn .= '<a class="dropdown-item" href="' . route('settlementletter.edit', $row['id']) . '">Edit</a>';
                }
                if (Helper::userCan(111, 'can_delete')) {
                    $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                }

                return Helper::userAllowed(111) ? $btn : '';
            })
            ->orderColumn('created_at', function ($query, $order) {
                $query->orderBy('created_at', $order);
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    $title = "Settlement Letter";
    return view('settlementletter.index', compact('title'));
}


public function add(): View
{
    return view('settlementletter.add');
}

public function save(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'drp_type'       => ['required', 'integer'],
        'name'           => ['required', 'string', 'unique:settlement_letters,name', 'max:50'],
        'subject'        => ['required', 'string'],
        'email_content'  => ['required', 'string'],
        'notice_format'  => ['required', 'string']
    ]);

    SettlementLetter::create($validated + ['status' => 1]);
    
    return to_route('settlementletter')->withSuccess('Settlement Letter Added Successfully..!!');
}

public function edit($id): View|RedirectResponse
{
    $settlementletter = SettlementLetter::find($id);
    if (!$settlementletter) return to_route('settlementletter')->withError('Settlement Letter Not Found..!!');

    return view('settlementletter.edit', compact('settlementletter'));
}

public function update(Request $request, $id): RedirectResponse
{
    $settlementletter = SettlementLetter::find($id);
    if (!$settlementletter) return to_route('settlementletter')->withError('Settlement Letter Not Found..!!');

    $validated = $request->validate([
        'drp_type'       => ['required', 'integer'],
        'name'           => ['required', 'string', 'unique:settlement_letters,name,' . $id, 'max:50'],
        'subject'        => ['required', 'string'],
        'email_content'  => ['required', 'string'],
        'notice_format'  => ['required', 'string']
    ]);

    $settlementletter->update($validated);
    
    return to_route('settlementletter')->withSuccess('Settlement Letter Updated Successfully..!!');
}

public function delete(Request $request): JsonResponse
{
    return Helper::deleteRecord(new SettlementLetter, $request->id);
}

public function getsettlementletterVariables(): JsonResponse
{
    return response()->json(SettlementLetterVariable::all());
}

}
