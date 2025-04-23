<?php
namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Blog;
use App\Models\BookAppointment;
use App\Models\CallBack;
use App\Models\Client;
use App\Models\Cms;
use App\Models\ContactUs;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\HomeCms;
use App\Models\News;
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
        $features            = Feature::where('status', 1)->orderby('id', 'ASC')->get()->toArray();
        $testimonials        = Testimonial::where('status', 1)->get()->toArray();
        $clients             = Client::where('status', 1)->get()->toArray();

        return view('front.home', compact('banners', 'frontHomecmsWelcome', 'frontHomecmsAbout', 'features', 'testimonials', 'clients'));
    }

    // ############ Show CMS ###########
    public function showCms(Request $request, $slug)
    {
        $cmsPages = [
            'about-us' => ['id' => 1, 'title' => 'About Us', 'view' => 'front.about-us'],
            'privacy-policy' => ['id' => 2, 'title' => 'Privacy Policy', 'view' => 'front.privacy-policy'],
            'terms-conditions' => ['id' => 3, 'title' => 'Terms & Conditions', 'view' => 'front.terms-conditions'],
            'rules' => ['id' => 4, 'title' => 'Rules', 'view' => 'front.rules'],
            'why-choose' => ['id' => 5, 'title' => 'Why Choose Us', 'view' => 'front.why-choose'],
            'return-cancel' => ['id' => 6, 'title' => 'Retun and Cancel', 'view' => 'front.return-cancel'],
            'shipping-delivery' => ['id' => 7, 'title' => 'Shipping and Delivery', 'view' => 'front.shipping-delivery'],
        ];

        if (!array_key_exists($slug, $cmsPages)) {
            abort(404);
        }

        $page = $cmsPages[$slug];
        $content = Cms::find($page['id']);

        return view($page['view'], compact('content'))->with('title', $page['title']);
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


    // ############ Book Appointment Form ###########
    public function faqs(Request $request)
    {
        $title = 'Faqs';

        $frontHomecmsFaqs   = HomeCms::where('id', 3)->first();
        $faqs = Faq::where('status', 1)->get();

        return view('front.faqs',compact('title', 'frontHomecmsFaqs', 'faqs'));
    }


    // ############ Blogs Appointment Form ###########
    public function blogs(Request $request)
    {
        $title = 'Blogs';
    
        $blogs = Blog::where('status', 1)->orderBy('id', 'desc')->paginate(6);
        $blogssidebar = Blog::where('status', 1)->limit(6)->orderby('id','DESC')->get();
    
        if ($request->ajax()) {
            return view('front.blogs_data', compact('blogs'))->render();
        }
    
        return view('front.blogs', compact('title', 'blogs', 'blogssidebar'));
    }


    // ############ News Appointment Form ###########
    public function news(Request $request)
    {
        $title = 'News Room';

        $news = News::where('status', 1)->orderBy('id', 'desc')->paginate(6);
        $newssidebar = News::where('status', 1)->limit(6)->orderby('id','DESC')->get();
    
        if ($request->ajax()) {
            return view('front.news_data', compact('news'))->render();
        }

        return view('front.news',compact('title', 'news','newssidebar'));
    }
}
