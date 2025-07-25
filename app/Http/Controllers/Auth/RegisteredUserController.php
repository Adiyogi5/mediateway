<?php

namespace App\Http\Controllers\Auth;

use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendWelComeEmail;
use App\Models\RegistrationOtp;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\OrganizationPermission;
use App\Models\Setting;
use App\Rules\ReCaptcha;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $googleRecaptchaData = Setting::where('setting_type', '8')
            ->get()
            ->pluck('filed_value', 'setting_name')
            ->toArray();

        $googleRecaptchaData['GOOGLE_RECAPTCHA_KEY'] = $googleRecaptchaData['GOOGLE_RECAPTCHA_KEY'] ?? env('GOOGLE_RECAPTCHA_KEY');

        return view('auth.register',compact('googleRecaptchaData'));
    }

    public function store(Request $request): RedirectResponse
    {
        $guard = $request->guard;

        // ------------------ 1. Admin Registration ------------------
        if ($guard == 'admin') {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        }

        // ------------------ 2. Organization Registration ------------------
        elseif ($guard == 'organization') {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:organizations',
                'mobile' => 'required|string|max:15|unique:organizations',
                'password' => 'required|string|min:6|confirmed',
                'g-recaptcha-response' => ['required', new ReCaptcha]
            ]);

            $userModel = '\App\Models\Organization';
            $user = $userModel::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => Hash::make($request->password),
                'organization_role_id' => 1,
            ]);

            // Assign full permissions to the organization
            $modules = \DB::table('organization_permission_modules')->pluck('module_id');
            $permissions = $modules->map(function ($moduleId) use ($user) {
                return [
                    'organization_id' => $user->id,
                    'module_id'       => $moduleId,
                    'can_view'        => 1,
                    'can_add'         => 1,
                    'can_edit'        => 1,
                    'can_delete'      => 1,
                    'allow_all'       => 1,
                ];
            });
            OrganizationPermission::insert($permissions->toArray());
        }

        // ------------------ 3. Individual / DRP Registration (via OTP) ------------------
        else {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:' . $guard . 's',
                'mobile' => 'required|string|max:15|unique:' . $guard . 's',
                'otp' => 'required|numeric|digits:6',
                'drp_type' => $guard === 'drp' ? 'required|in:' . implode(',', array_keys(config('constant.drp_type'))) : 'nullable',
            ]);

            $checkOtp = RegistrationOtp::firstWhere(['mobile' => $request->mobile, 'otp' => $request->otp]);

            if (!$checkOtp) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'otp' => 'Incorrect OTP..!!',
                ]);
            }

            if (Carbon::now()->isAfter($checkOtp->expire_at)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'otp' => 'Your OTP has been expired',
                ]);
            }

            $userModel = '\App\Models\\' . ucfirst($guard);
            $user = $userModel::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'email' => $request->email,
                'mobile' => $request->mobile,
                'otp' => $request->otp,
                'drp_type' => $guard === 'drp' ? $request->drp_type : null,
            ]);

            // Clean up OTP
            RegistrationOtp::where('mobile', $request->mobile)->delete();
        }

        // Log in the newly registered user
        Auth::guard($guard)->login($user, true);

        // Send Welcome Email
        SendWelComeEmail::dispatch($user, $request->site_settings);

        return redirect()->intended(route($guard . '.dashboard'))
            ->withSuccess("Successfully Registered, Welcome $user->name..!!");
    }
    
    // public function store(Request $request): RedirectResponse
    // {
    //     $guard = $request->guard; 

    //     if ($guard == 'admin') {
    //         // Admin registration with password
    //         $request->validate([
    //             'name' => 'required|string|max:255',
    //             'email' => 'required|string|email|max:255|unique:users',
    //             'password' => 'required|string|min:6|confirmed',
    //         ]);

    //         $user = User::create([
    //             'name' => $request->name,
    //             'email' => $request->email,
    //             'password' => Hash::make($request->password),
    //         ]);

    //     } else {
    //         // Other users register with name, email, mobile, and OTP
    //         $request->validate([
    //             'name' => 'required|string|max:255',
    //             'email' => 'required|string|email|max:255|unique:' . $guard . 's',
    //             'mobile' => 'required|string|max:15|unique:' . $guard . 's',
    //             'otp' => 'required|numeric|digits:6', // Validate OTP
    //             'drp_type' => $guard === 'drp' ? 'required|in:' . implode(',', array_keys(config('constant.drp_type'))) : 'nullable',
    //         ]);

    //         $checkOtp = RegistrationOtp::firstWhere(['mobile' => $request->mobile, 'otp' => $request->otp]);
    //         if (!$checkOtp) {
    //             throw ValidationException::withMessages([
    //                 'otp' => 'Incorrect OTP..!!',
    //             ]);
    //         }
    
    //         if ($checkOtp && Carbon::now()->isAfter($checkOtp->expire_at)) {
    //             throw ValidationException::withMessages([
    //                 'otp' => 'Your OTP has been expired',
    //             ]);
    //         }

    //         $userModel = '\App\Models\\' . ucfirst($guard);
    //         $user = $userModel::create([
    //             'name' => $request->name,
    //             'slug' => Str::slug($request->name),
    //             'email' => $request->email,
    //             'mobile' => $request->mobile,
    //             'otp' => $request->otp, // Store OTP for verification
    //             'organization_role_id' => $guard === 'organization' ? 1 : null, // Store only if Organization
    //             'drp_type' => $guard === 'drp' ? $request->drp_type : null, // Store only if DRP
    //         ]);

    //         if ($guard === 'organization') {
    //             $modules = \DB::table('organization_permission_modules')->pluck('module_id');
            
    //             $data = $modules->map(function ($moduleId) use ($user) {
    //                 return [
    //                     'organization_id' => $user->id,
    //                     'module_id'       => $moduleId,
    //                     'can_view'        => 1,
    //                     'can_add'         => 1,
    //                     'can_edit'        => 1,
    //                     'can_delete'      => 1,
    //                     'allow_all'       => 1,
    //                 ];
    //             });
            
    //             OrganizationPermission::insert($data->toArray());
    //         }            
            
    //     }

    //     Auth::guard($guard)->login($user, true);

    //     RegistrationOtp::where('mobile', $request->mobile)->delete();
    //     SendWelComeEmail::dispatch($user, $request->site_settings);

    //     return redirect()->intended(route($guard.'.dashboard'))->withSuccess("Successfully Registered, Welcome $user->name..!!");
    // }
}
