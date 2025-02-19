<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Client;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Client::select('id', 'name', 'image', 'status', 'created_at');

            return Datatables::of($data)
                ->editColumn('image', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(111, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('clients.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userCan(111, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    return Helper::userAllowed(111) ? $btn : '';
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
        return view('clients.index');
    }

    public function add(): View
    {
        return view('clients.add');
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:50'],
            'status'        => ['required', 'integer'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:2048']
        ]);

        $data = [...$validated, 'image' => 'admin/avatar.png'];
        if ($request->hasFile('image')) {
            $data['image']        = Helper::saveFile($request->file('image'), 'clients');
        }

        Client::create($data);
        return to_route('clients')->withSuccess('Client Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $client = Client::find($id);
        if (!$client) return to_route('clients')->withError('Client Not Found..!!');

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $client =  Client::find($id);
        if (!$client) return to_route('clients')->withError('Client Not Found..!!');

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:50'],
            'status'        => ['required', 'integer'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:2048']
        ]);

        if ($request->hasFile('image')) {
            Helper::deleteFile($client->image);
            $data['image']        = Helper::saveFile($request->file('image'), 'clients');
        }

        $client->update($data);
        return to_route('clients')->withSuccess('Client Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Client, $request->id);
    }
}
