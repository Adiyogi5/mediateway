<?php
namespace App\Http\Controllers;

use App\Models\BookAppointment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class BookAppointmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BookAppointment::select(['id', 'name', 'mobile', 'email', 'datestart', 'dateend', 'created_at']);
            return DataTables::of($data)
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-M-Y'); // Format as dd-Month-Year
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info view_bookappointments" data-id="' . $row->id . '">View</button>
                            <button class="btn btn-sm btn-danger delete_record" data-id="' . $row->id . '">Delete</button>';
                })
                ->rawColumns(['action','created_at'])
                ->make(true);
        }

        return view('bookappointments.index');
    }

    public function show($id)
    {
        $bookappointment = BookAppointment::findOrFail($id);
       
        return view('bookappointments.show', compact('bookappointment'));
    }

    public function destroy($id)
    {
        $bookappointment = BookAppointment::findOrFail($id);
        $bookappointment->delete();
        return response()->json(['success' => true]);
    }
}
