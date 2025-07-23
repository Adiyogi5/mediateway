<?php
namespace App\Http\Controllers\Drp;

use App\Exports\SampleBulkUpdateCaseExport;
use App\Http\Controllers\Controller;
use App\Imports\BulkUpdateCaseImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class CaseBulkUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function casebulkupdate(Request $request): View
    {
        $title = 'Bulk Update Cases';

        $drp = auth('drp')->user();
        // Ensure the user is authenticated and has drp_type == 3
        if (! auth('drp')->check() || auth('drp')->user()->drp_type != 3) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
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

            // Clear previous unmatched
            BulkUpdateCaseImport::$unmatchedLoans = [];

            Excel::import(new BulkUpdateCaseImport(), $request->file('file'));

            Log::info('Import process completed.');

            // Check if any loan numbers did not match
            $unmatched = BulkUpdateCaseImport::$unmatchedLoans;

            if (! empty($unmatched)) {
                $message = 'Some loan numbers were not found: ' . implode(', ', array_unique($unmatched));
                return redirect()->route('drp.cases.casebulkupdate')
                    ->with('warning', $message)
                    ->with('success', 'Bulk Update File imported, but with some unmatched loans.');
            }

            return redirect()->route('drp.cases.casebulkupdate')
                ->with('success', 'Bulk Update File imported successfully.');

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
