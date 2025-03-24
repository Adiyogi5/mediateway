<?php
namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BookAppointment;
use App\Models\CallBack;
use App\Models\Client;
use App\Models\Cms;
use App\Models\ContactUs;
use App\Models\Feature;
use App\Models\HomeCms;
use App\Rules\ReCaptcha;
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

    // ############ Show CMS ###########
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

    // ############ Call BAck Request Form ###########
    public function callback(Request $request)
    {
        $title = 'Call Back';
        return view('front.callback',compact('title'));
    }

    public function requestcallback(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'min:6', 'max:100'],
            'mobile'    => ['required'],
            'datetime' => ['required'],
        ]);

        CallBack::create($validated);

        return to_route('front.callback')->withSuccess('Call Back Request Send successfully..!!');
    }


    // ############ Book Appointment Form ###########
    public function bookappointment(Request $request)
    {
        $title = 'Book Appointment';

        return view('front.bookappointment',compact('title'));
    }

    public function requestbookappointment(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'min:6', 'max:100'],
            'mobile'    => ['required'],
            'email'     => ['required'],
            'datestart' => ['required'],
            'dateend'   => ['required'],
        ]);

        BookAppointment::create($validated);

        return to_route('front.bookappointment')->withSuccess('Your Appointment Booked successfully..!!');
    }

    // ############ Contact Us Form ###########
    public function contactus(Request $request)
    {
        $title = 'Contact Us';

        return view('front.contactus',compact('title'));
    }

    public function submitcontactus(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => ['required'],
            'last_name'    => ['required'],
            'mobile'   => ['required'],
            'email'   => ['required', 'string', 'min:10', 'max:100', 'email'],
            'subject' => ['required', 'string', 'min:10', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:1000'],
            'g-recaptcha-response' => ['required', new ReCaptcha]
        ]);

        ContactUs::create($validated);
        return to_route('front.contactus')->withSuccess('Message saved successfully..!!');
    }
}
