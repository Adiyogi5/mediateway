<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Amenity;
use App\Models\Banner;
use App\Models\City;
use App\Models\Client;
use App\Models\Cms;
use App\Models\ContactUs;
use App\Models\Customer;
use App\Models\Faq;
use App\Models\Library;
use App\Models\RegistrationOtp;
use App\Models\Review;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        $banners = Banner::where('status',1)->get()->toArray();
        $testimonials = Testimonial::where('status',1)->get()->toArray();
       
        $clients = Client::where('status',1)->get()->toArray();

        return view('front.home', compact('banners', 'testimonials','clients'));
    }

    public function contactUs(Request $request)
    {
        return view('front.contact-us');
    }

    public function contactUsSave(Request $request)
    {
        $validated = $request->validate([
            'type'      => ['required', 'integer', 'min:1', 'in:1,2,3'],
            'name'      => ['required', 'string', 'min:6', 'max:100'],
            'email'     => ['required', 'string', 'min:10', 'max:100', 'email'],
            'message'   => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        ContactUs::create($validated);
        return to_route('front.contact-us')->withSuccess('Message saved successfully..!!');
    }

    public function showCms(Request $request, $slug)
    {
        switch ($slug) {
            case 'about-us':
                $content = Cms::find(2);
                $pageName = 'About Us';
                return view('front.about-us', compact('content', 'pageName'));

            case 'privacy-policy':
                $content = Cms::find(3);
                $pageName = 'Privacy Policy';
                return view('front.privacy-policy', compact('content', 'pageName'));

            case 'terms-condition':
                $content = Cms::find(4);
                $pageName = 'Terms Condition';
                return view('front.terms-condition', compact('content', 'pageName'));

            default:
                abort(404);
                break;
        }
    }

}
