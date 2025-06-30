<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\RegistrationOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;


class AuthenticatedSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['guest'])->except('destroy');
    }

    public function create($guard = 'admin'): View
    {
        // Separate login pages for each user type
        $view = match ($guard) {
            'individual'   => 'auth.login-front',
            'organization' => 'auth.login-front',
            'drp'          => 'auth.login-front',
            default        => 'auth.login', // Admin login
        };

        return view($view, ['guard' => $guard]);
    }

    public function store(LoginRequest $request, $guard = 'admin'): RedirectResponse
    {
        if ($guard == 'admin') {
            // ------------------- Admin Login (email + password) -------------------
            $credentials = $request->validate([
                'email'    => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::guard($guard)->attempt($credentials, $request->filled('remember'))) {
                return back()->withError("Invalid Login Credential..!!")
                            ->withInput($request->only('email', 'remember'));
            }
        } 
        elseif ($guard == 'organization') {
            // ------------------- Organization Login (mobile + password) -------------------
            $request->validate([
                'mobile'   => 'required|string|max:15',
                'password' => 'required|string|min:6',
            ]);

            $userModel = '\App\Models\\' . ucfirst($guard);
            $user = $userModel::where('mobile', $request->mobile)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return back()->withError("Invalid Mobile or Password..!!");
            }

            Auth::guard($guard)->login($user);
        } 
        else {
            // ------------------- Individual / DRP Login (mobile + OTP) -------------------
            $request->validate([
                'mobile' => 'required|string|max:15',
                'otp'    => 'required|numeric|digits:6',
            ]);

            $checkOtp = RegistrationOtp::where('mobile', $request->mobile)
                                    ->where('otp', $request->otp)
                                    ->first();

            if (!$checkOtp) {
                return back()->withError("Invalid OTP..!!");
            }

            $userModel = '\App\Models\\' . ucfirst($guard);
            $user = $userModel::where('mobile', $request->mobile)->first();

            if (!$user) {
                return back()->withError("User not found..!!");
            }

            Auth::guard($guard)->login($user);
            RegistrationOtp::where('mobile', $request->mobile)->delete();
        }

        // ------------------- Regenerate session and redirect -------------------
        $request->session()->regenerate();

        $redirectRoute = $guard === 'admin' ? 'dashboard' : $guard . '.dashboard';

        return redirect()->intended(route($redirectRoute))
                        ->withSuccess("Successfully Logged In..!!");
    }

    
    public function destroy(Request $request): RedirectResponse
    {
        // Check for any logged-in user across all guards
        $guards = ['admin', 'individual', 'organization', 'drp'];
        $loggedOut = false;
    
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Auth::guard($guard)->logout();
                $loggedOut = true;
                break;
            }
        }
    
        // Only invalidate session if at least one guard was logged in
        if ($loggedOut) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            Session::forget('locked');
    
            // Add a success message
            return to_route('front.home')->with('success', 'You have been logged out successfully.');
        }
    
        return to_route('front.home');
    }      

}
