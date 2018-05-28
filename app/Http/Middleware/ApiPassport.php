<?php

namespace App\Http\Middleware;

use App\Models\App;
use Closure;
use DB;

class ApiPassport
{

    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('app_token')) {
            if (!$this->checkAppToken($request)) {
                $message = '无效密钥';
            }
        } elseif ($request->has(['app_id', 'without_passport'])) {
            if (!$this->checkSignature($request)) {
                $message = '无效签名';
            }
        } else {
            $message = '未通过验证';
        }
        if (isset($message)) {
            return app('ApiResponse')->makeErrorResponse($message, 503);
        } else {
            return $next($request);
        }
    }

    private function checkAppToken($request)
    {
        $appToken = $request->app_token;
        $appUser = DB::table('app_token')->where([['app_token', '=', $appToken], ['expiration', '>', time()]])->first();
        if (empty($appUser)) {
            return false;
        }
        $appId = $appUser->app_id;
        $staffSn = $appUser->staff_sn;
        $request->offsetSet('app_id', $appId);
        $request->offsetSet('current_staff_sn', $staffSn);
        return true;
    }

    private function checkSignature($request)
    {
        try {
            $timestamp = $request->timestamp;
            $appId = $request->app_id;
            $app = App::find($appId);
            $signature = $request->signature;
            $request->offsetSet('current_staff_sn', '000000');
            return md5($app->app_ticket . $timestamp) == $signature;
        } catch (\Exception $e) {
            return false;
        }
    }

}
