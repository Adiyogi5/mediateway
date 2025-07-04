<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistrationOtp;
use App\Models\SmsCount;
use App\Rules\CheckUnique;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class CommonController extends Controller
{
    public function sendOtp(Request $request)
    {
        $guard = $request->guard;
   
        $table = match ($guard) {
            'individual'   => 'individuals',
            'organization' => 'organizations',
            'drp'          => 'drps',
            default        => 'individuals', // fallback
        };

        $rules = ['mobile' => ['required', 'numeric', 'min:10', 'regex:' . config('constant.phoneRegExp')]];

        if ($request->is_register) {
            $rules['mobile'][] = new CheckUnique($table);
        } else {
            $rules['mobile'][] = 'exists:' . $table . ',mobile';
        }

        $messages = [
            'mobile.exists' => "Account doesn't exist",
            'mobile.regex'  => "Please enter a valid Indian mobile number.",
        ];

        $validation = Validator::make($request->all(), ['mobile' => $rules], $messages);
        if ($validation->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validation->errors()->first('mobile'),
                'data'    => [
                    'mobile' => $validation->errors()->first('mobile'),
                ],
            ]);
        }

        $approved_sms_count = SmsCount::where('count', '>', 0)->first();

        if (! $approved_sms_count) {
            return response()->json([
                'status'  => false,
                'message' => "OTP can't be sent because your SMS quota is empty.",
            ], 422);
        }

        $old = RegistrationOtp::where('mobile', $request->mobile)->where('expire_at', '>', now())->first();
        if ($old) {
            $otp = $old->otp;
        } else {
            // $otp = $request->mobile == "8741066111" ? 123456 : random_int(100000, 999999);
            $otp = random_int(100000, 999999);
            RegistrationOtp::where('mobile', $request->mobile)->delete();
            RegistrationOtp::create([
                'mobile'    => $request->mobile,
                'otp'       => $otp,
                'expire_at' => now()->addMinutes(10),
            ]);
        }

        $mobile = '91' . $request->mobile;
        $message =  "Hello User, Your login verification code is $otp. Do not share it with anyone. Team Mediateway";
        $smsResponse = Http::withHeaders(['apiKey' => 'aHykmbPNHOE9KGE',])->post('https://api.bulksmsadmin.com/BulkSMSapi/keyApiSendSMS/sendSMS', [
                                "sender"      => "MDTWAY",
                                "peId"        => "1001292642501782120",
                                "teId"        => "1007183533861090202",
                                "message"     => $message,
                                "smsReciever" => [["reciever" => $mobile]],
                            ]);
        
        if ($smsResponse) {
            $approved_sms_count->decrement('count');
            return response()->json([
                'status'  => true,
                'message' => 'Enter OTP received on your mobile!',
                'data'    => '',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => "OTP can't be sent, please retry after some time.",
                'data'    => '',
            ], 422);
        }
    }

}
