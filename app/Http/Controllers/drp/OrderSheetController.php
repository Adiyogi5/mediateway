<?php

namespace App\Http\Controllers\Drp;

use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Models\OrderSheet;
use App\Models\OrderSheetVariable;
use Illuminate\View\View;
use Illuminate\Support\Str;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderSheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function index(Request $request): View|JsonResponse|RedirectResponse
    {
        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || !in_array(auth('drp')->user()->drp_type, [1, 5])){
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        
        if ($request->ajax()) {
            $data = OrderSheet::select('id', 'drp_type', 'name', 'status', 'created_at');

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
                    
                    // if (Helper::userCan(111, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('drp.ordersheet.edit', $row['id']) . '">Edit</a>';
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

        $title = "Order Sheet";
        return view('drp.ordersheet.index', compact('title'));
    }


public function add(): View|RedirectResponse
{
    // Ensure the user is authenticated and has drp_type == 1
    if (!auth('drp')->check() || !in_array(auth('drp')->user()->drp_type, [1, 5])){
        return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
    }
    
    $title = "Add Order Sheet";
    return view('drp.ordersheet.add', compact('title'));
}

public function save(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'drp_type'       => ['required', 'integer'],
        'name'           => ['required', 'string', 'unique:order_sheets,name', 'max:50'],
        'subject'        => ['required', 'string'],
        'email_content'  => ['required', 'string'],
        'notice_format'  => ['required', 'string']
    ]);

    OrderSheet::create($validated + ['status' => 1]);
    
    return to_route('drp.ordersheet')->withSuccess('Order Sheet Added Successfully..!!');
}

public function edit($id): View|RedirectResponse
{
    // Ensure the user is authenticated and has drp_type == 1
    if (!auth('drp')->check() || !in_array(auth('drp')->user()->drp_type, [1, 5])){
        return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
    }
    
    $orderSheet = OrderSheet::find($id);
    if (!$orderSheet) return to_route('drp.ordersheet')->withError('Order Sheet Not Found..!!');

    $title = "Add Order Sheet";
    return view('drp.ordersheet.edit', compact('orderSheet','title'));
}

public function update(Request $request, $id): RedirectResponse
{
    $orderSheet = OrderSheet::find($id);
    if (!$orderSheet) return to_route('drp.ordersheet')->withError('Order Sheet Not Found..!!');

    $validated = $request->validate([
        'drp_type'       => ['required', 'integer'],
        'name'           => ['required', 'string', 'unique:order_sheets,name,' . $id, 'max:50'],
        'subject'        => ['required', 'string'],
        'email_content'  => ['required', 'string'],
        'notice_format'  => ['required', 'string']
    ]);

    $orderSheet->update($validated);
    
    return to_route('drp.ordersheet')->withSuccess('Order Sheet Updated Successfully..!!');
}

public function delete(Request $request): JsonResponse
{
    return Helper::deleteRecord(new OrderSheet, $request->id);
}

public function getordersheetVariables(): JsonResponse
{
    return response()->json(OrderSheetVariable::all());
}

}
