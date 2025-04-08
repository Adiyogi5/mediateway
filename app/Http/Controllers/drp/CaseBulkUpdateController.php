<?php

namespace App\Http\Controllers\Drp;

use App\Http\Controllers\Controller;
use App\Exports\SampleBulkUpdateCaseExport;
use App\Imports\BulkUpdateCaseImport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class CaseBulkUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function casebulkupdate(Request $request): View
    {
        $title = 'Bulk Update Cases';

        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 3) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }

        return view('drp.cases.casebulkupdate', compact('title'));
    }

    public function importBulkUpdateExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx,csv|max:2048', 
        ]);

        try {
            Log::info('Import function triggered, file received.');
            Excel::import(new BulkUpdateCaseImport(), $request->file('file'));
            Log::info('Import process completed.');
            
            return redirect()->route('drp.cases.casebulkupdate')->with('success', 'Bulk Update File imported successfully.');

        } catch (\Exception $e) {
            Log::error('File import failed: ' . $e->getMessage());
            return back()->with('error', 'File import failed. Please check the format.');
        }
    }

    public function downloadBulkUpdateSample()
    {
        return Excel::download(new SampleBulkUpdateCaseExport, 'sample_file_case_bulk_update.xlsx');
    }

}