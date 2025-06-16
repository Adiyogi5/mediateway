<?php
namespace App\Http\Controllers;

use App\Models\HomeCms;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function index(Request $request)
    {
        $title = 'Services';
        $frontHomecmsService   = HomeCms::where('id', 4)->first();

        return view('front.services.services', compact('title', 'frontHomecmsService'));
    }

    public function msme(Request $request)
    {
        $title = 'Services';

        return view('front.services.msme', compact('title'));
    }

    public function mediation(Request $request)
    {
        $title = 'Services';

        return view('front.services.mediation', compact('title'));
    }

    public function conciliation(Request $request)
    {
        $title = 'Services';

        return view('front.services.conciliation', compact('title'));
    }

    public function arbitration(Request $request)
    {
        $title = 'Services';

        return view('front.services.arbitration', compact('title'));
    }

    public function odr(Request $request)
    {
        $title = 'Services';

        return view('front.services.odr', compact('title'));
    }

    public function lokadalat(Request $request)
    {
        $title = 'Services';

        return view('front.services.lokadalat', compact('title'));
    }


}
