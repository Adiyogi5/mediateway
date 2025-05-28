<?php

namespace App\Http\Controllers\Drp;

use App\Http\Controllers\Controller;

use App\Helper\Helper;
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
        $this->middleware('auth:drp');
    }

    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        if (!auth('drp')->check() || !in_array(auth('drp')->user()->drp_type, [4, 5])) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        
        $drp = auth('drp')->user();

        if ($request->ajax()) {
            $data = SettlementLetter::select('id', 'drp_type', 'name', 'status', 'created_at')
                        ->where('drp_type', $drp->drp_type);

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
                    $btn .= '<a class="dropdown-item" href="' . route('drp.settlementletter.edit', $row['id']) . '">Edit</a>';
                    // $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    return $btn;
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $title = "Settlement Agreements";
        return view('drp.settlementletter.index', compact('title'));
    }


public function add(): View|RedirectResponse
{
    $drp = auth('drp')->user();
   
    if (!auth('drp')->check() || !in_array(auth('drp')->user()->drp_type, [4, 5])) {
        return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
    }
    
    $title = "Add Settlement Agreement";
    return view('drp.settlementletter.add', compact('title','drp'));
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
    
    return to_route('drp.settlementletter')->withSuccess('Settlement Agreement Added Successfully..!!');
}

public function edit($id): View|RedirectResponse
{
    $drp = auth('drp')->user();
    
    if (!auth('drp')->check() || !in_array(auth('drp')->user()->drp_type, [4, 5])) {
        return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
    }
    
    $settlementletter = SettlementLetter::find($id);
    if (!$settlementletter) return to_route('drp.settlementletter')->withError('Settlement Agreement Not Found..!!');

    $title = "Edit Settlement Agreement";
    return view('drp.settlementletter.edit', compact('settlementletter','title','drp'));
}

public function update(Request $request, $id): RedirectResponse
{
    $settlementletter = SettlementLetter::find($id);
    if (!$settlementletter) return to_route('drp.settlementletter')->withError('Settlement Agreement Not Found..!!');

    $validated = $request->validate([
        'drp_type'       => ['required', 'integer'],
        'name'           => ['required', 'string', 'unique:settlement_letters,name,' . $id, 'max:50'],
        'subject'        => ['required', 'string'],
        'email_content'  => ['required', 'string'],
        'notice_format'  => ['required', 'string']
    ]);

    $settlementletter->update($validated);
    
    return to_route('drp.settlementletter')->withSuccess('Settlement Agreement Updated Successfully..!!');
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
