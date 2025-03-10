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

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $guard = $request->guard; 

        if ($guard == 'admin') {
            // Admin registration with password
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

        } else {
            // Other users register with name, email, mobile, and OTP
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:' . $guard . 's',
                'mobile' => 'required|string|max:15|unique:' . $guard . 's',
                'otp' => 'required|numeric|digits:6', // Validate OTP
            ]);

            // $checkOtp = RegistrationOtp::firstWhere(['mobile' => $request->mobile, 'otp' => $request->otp]);
            // if (!$checkOtp) {
            //     throw ValidationException::withMessages([
            //         'otp' => 'Incorrect OTP..!!',
            //     ]);
            // }
    
            // if ($checkOtp && Carbon::now()->isAfter($checkOtp->expire_at)) {
            //     throw ValidationException::withMessages([
            //         'otp' => 'Your OTP has been expired',
            //     ]);
            // }

            $userModel = '\App\Models\\' . ucfirst($guard);
            $user = $userModel::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'email' => $request->email,
                'mobile' => $request->mobile,
                'otp' => $request->otp, // Store OTP for verification
            ]);
        }

        Auth::guard($guard)->login($user, true);

        RegistrationOtp::where('mobile', $request->mobile)->delete();
        SendWelComeEmail::dispatch($user, $request->site_settings);

        return redirect()->intended(route($guard.'.dashboard'))->withSuccess("Successfully Registered, Welcome $user->name..!!");
    }
}
