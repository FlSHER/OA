<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class ApiPassport {

    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($request->has('app_token') && $this->checkAppToken($request)) {
            return $next($request);
        }
        $message = '无效密钥';
        return app('ApiResponse')->makeErrorResponse($message, 503);
    }

    private function checkAppToken($request) {
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

}
