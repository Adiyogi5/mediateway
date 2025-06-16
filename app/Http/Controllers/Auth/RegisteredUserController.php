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
use Carbon\Carbon;

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
                'drp_type' => $guard === 'drp' ? 'required|in:' . implode(',', array_keys(config('constant.drp_type'))) : 'nullable',
            ]);

            $checkOtp = RegistrationOtp::firstWhere(['mobile' => $request->mobile, 'otp' => $request->otp]);
            if (!$checkOtp) {
                throw ValidationException::withMessages([
                    'otp' => 'Incorrect OTP..!!',
                ]);
            }
    
            if ($checkOtp && Carbon::now()->isAfter($checkOtp->expire_at)) {
                throw ValidationException::withMessages([
                    'otp' => 'Your OTP has been expired',
                ]);
            }

            $userModel = '\App\Models\\' . ucfirst($guard);
            $user = $userModel::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'email' => $request->email,
                'mobile' => $request->mobile,
                'otp' => $request->otp, // Store OTP for verification
                'organization_role_id' => $guard === 'organization' ? 1 : null, // Store only if Organization
                'drp_type' => $guard === 'drp' ? $request->drp_type : null, // Store only if DRP
            ]);

            if ($guard === 'organization') {
                $modules = \DB::table('organization_permission_modules')->pluck('module_id');
            
                $data = $modules->map(function ($moduleId) use ($user) {
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
            
                OrganizationPermission::insert($data->toArray());
            }            
            
        }

        Auth::guard($guard)->login($user, true);

        RegistrationOtp::where('mobile', $request->mobile)->delete();
        SendWelComeEmail::dispatch($user, $request->site_settings);

        return redirect()->intended(route($guard.'.dashboard'))->withSuccess("Successfully Registered, Welcome $user->name..!!");
    }
}
