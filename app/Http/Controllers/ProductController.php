<?php
namespace App\Http\Controllers;

use App\Models\HomeCms;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function digitalroom(Request $request)
    {
        $title = 'Products';
        $frontHomecmsdigitalroom   = HomeCms::where('id', 5)->first();

        return view('front.products.digitalroom', compact('title', 'frontHomecmsdigitalroom'));
    }

    public function odrplatform(Request $request)
    {
        $title = 'Products';
        $frontHomecmsodrplatform   = HomeCms::where('id', 6)->first();

        return view('front.products.odrplatform', compact('frontHomecmsodrplatform'));
    }

}
