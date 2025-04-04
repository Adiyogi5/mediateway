<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\ServiceFee;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ServiceFeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = ServiceFee::select('id', 'ticket_size_min', 'ticket_size_max', 'cost','status', 'created_at');

            return Datatables::of($data)
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(111, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('servicefee.edit', $row['id']) . '">Edit</a>';
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
        
        return view('servicefee.index');
    }

    public function add(): View
    {
        return view('servicefee.add');
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_size_min'          => ['required'],
            'ticket_size_max'          => ['required'],
            'cost'                     => ['required'],
            'status'                   => ['required', 'integer'],
        ]);

        $data = [...$validated];
        ServiceFee::create($data);
        
        return to_route('servicefee')->withSuccess('Service Fee Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $servicefee = ServiceFee::find($id);
        if (!$servicefee) return to_route('servicefee')->withError('Service Fee Not Found..!!');

        return view('servicefee.edit', compact('servicefee'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $servicefee =  ServiceFee::find($id);
        if (!$servicefee) return to_route('servicefee')->withError('Service Fee Not Found..!!');

        $data = $request->validate([
            'ticket_size_min'          => ['required'],
            'ticket_size_max'          => ['required'],
            'cost'                     => ['required'],
            'status'                   => ['required', 'integer'],
        ]);

        $servicefee->update($data);
        return to_route('servicefee')->withSuccess('Service Fee Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new ServiceFee, $request->id);
    }
}
