<?php
namespace App\Http\Controllers;

use App\Models\CallBack;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class CallBackController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CallBack::select(['id', 'name', 'datetime', 'mobile', 'created_at']);
            return DataTables::of($data)
                ->addColumn('datetime', function ($row) {
                    return Carbon::parse($row->datetime)->format('d-M-Y');
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-M-Y'); // Format as dd-Month-Year
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info view_callback" data-id="' . $row->id . '">View</button>
                            <button class="btn btn-sm btn-danger delete_record" data-id="' . $row->id . '">Delete</button>';
                })
                ->rawColumns(['action','datetime','created_at'])
                ->make(true);
        }

        return view('callbacks.index');
    }

    public function show($id)
    {
        $callback = CallBack::findOrFail($id);
        return view('callbacks.show', compact('callback'));
    }

    public function destroy($id)
    {
        $callback = CallBack::findOrFail($id);
        $callback->delete();
        return response()->json(['success' => true]);
    }
}
