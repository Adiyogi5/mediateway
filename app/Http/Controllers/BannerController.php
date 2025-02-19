<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;

class BannerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View | JsonResponse
    {
        if ($request->ajax()) {
            $data = Banner::query();

            return Datatables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(112, 'can_edit')) $btn .= '<button class="dropdown-item edit" data-all="' . htmlspecialchars(json_encode($row)) . '">Edit</button>';
                    if (Helper::userCan(112, 'can_delete')) $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    return Helper::userAllowed(112) ? $btn : '';
                })
                ->editColumn('image', function ($row) {
                    $fileUrl = asset($row['image']);
                    $fileExtension = pathinfo($row['image'], PATHINFO_EXTENSION);
                
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png'])) {
                        return '<img src="' . $fileUrl . '" class="img-thumbnail" style="height: 80px; max-width: 400px;" alt="Image">';
                    } elseif (in_array($fileExtension, ['mp4'])) {
                        return '<video class="img-thumbnail" controls style="height: 80px; max-width: 400px;">
                                    <source src="' . $fileUrl . '" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>';
                    } else {
                        return 'Unsupported File Type';
                    }
                })                
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->editColumn('created_at', fn ($row) => $row->created_at->format('d F, Y'))
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
        return view('banners.index');
    }

    public function save(Request $request): JsonResponse
    {
        return Helper::checkValid([
            'url'       => ['nullable', 'url', 'max:100'],
            'status'    => ['required', 'integer'],
            'image'     => ['required', 'file', 'mimes:jpg,png,jpeg,mp4', 'max:2048'],

        ], function ($validator) use ($request) {
            if ($request->hasFile('image')) {
                $data = $validator->validated();
                $data['image'] = Helper::saveFile($request->file('image'), 'banners');
                Banner::create($data);
            }

            return response()->json([
                'status'    => true,
                'message'   => 'Banner Added Successfully',
                'data'      => '',
            ]);
        });
    }

    public function update(Request $request): JsonResponse
    {
        $banner = Banner::find($request->id);
        if (!$banner)  return response()->json([
            'status'    => true,
            'message'   => 'Banner Not Found!!',
            'data'      => '',
        ]);

        return Helper::checkValid([
            'url'       => ['nullable', 'string', 'url'],
            'status'    => ['required', 'integer'],
            'image'     => ['nullable', 'file', 'mimes:jpg,png,jpeg,mp4', 'max:2048'],
        ], function ($validator) use ($request, $banner) {
            $data = $validator->validated();
            if ($request->hasFile('image')) {
                Helper::deleteFile($banner->image);
                $data['image'] = Helper::saveFile($request->file('image'), 'banners');
            }

            $banner->update($data);
            return response()->json([
                'status'    => true,
                'message'   => 'Banner Updated Successfully..!!',
                'data'      => '',
            ]);
        });
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Banner, $request->id);
    }
}
