<?php
namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Client;
use App\Models\Cms;
use App\Models\ContactUs;
use App\Models\Feature;
use App\Models\HomeCms;
use App\Models\State;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        $banners             = Banner::where('status', 1)->get()->toArray();
        $frontHomecmsWelcome = HomeCms::where('id', 1)->first();
        $frontHomecmsAbout   = HomeCms::where('id', 2)->first();
        $features            = Feature::where('status', 1)->get()->toArray();
        $testimonials        = Testimonial::where('status', 1)->get()->toArray();
        $clients             = Client::where('status', 1)->get()->toArray();

        return view('front.home', compact('banners', 'frontHomecmsWelcome', 'frontHomecmsAbout', 'features', 'testimonials', 'clients'));
    }

    public function contactUs(Request $request)
    {
        return view('front.contact-us');
    }

    public function contactUsSave(Request $request)
    {
        $validated = $request->validate([
            'type'    => ['required', 'integer', 'min:1', 'in:1,2,3'],
            'name'    => ['required', 'string', 'min:6', 'max:100'],
            'email'   => ['required', 'string', 'min:10', 'max:100', 'email'],
            'message' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        ContactUs::create($validated);
        return to_route('front.contact-us')->withSuccess('Message saved successfully..!!');
    }

    public function showCms(Request $request, $slug)
    {
        switch ($slug) {
            case 'about-us':
                $content  = Cms::find(1);
                $title = 'About Us';
                return view('front.about-us', compact('content', 'title'));

            case 'privacy-policy':
                $content  = Cms::find(2);
                $title = 'Privacy Policy';
                return view('front.privacy-policy', compact('content', 'title'));

            case 'terms-conditions':
                $content  = Cms::find(3);
                $title = 'Terms Condition';
                return view('front.terms-conditions', compact('content', 'title'));

            case 'rules':
                $content  = Cms::find(4);
                $title = 'Rules';
                return view('front.rules', compact('content', 'title'));

            default:
                abort(404);
                break;
        }
    }

}
