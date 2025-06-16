<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use App\Models\SmsCount;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SettingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $data = Setting::select('setting_name', 'filed_value')->get()->pluck('filed_value', 'setting_name')->toArray();

        $approved_sms_summary = SmsCount::selectRaw('SUM(count) as total_count, SUM(credited) as total_credited')->first();

        if (!$request->is('api/*')) {
            View::share('site_settings', $data);
            View::share('approved_sms_summary', $approved_sms_summary);
            View::share('config', [
                //   
            ]);
        }
        
        $request->merge(['site_settings' => $data]);
        return $next($request);
    }
}
