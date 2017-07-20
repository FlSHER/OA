<?php

namespace App\Http\Middleware;

use Closure;

class Rbac {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if (app('CurrentUser')->isLogin()) {
            return $next($request);
        }
        return $this->redirectToLoginPage();
    }

    private function isLogin() {
        $adminInfo = app();
        return !empty($adminInfo);
    }

    private function redirectToLoginPage() {
        $url = trim(url()->current(), '/');
        if ($url == trim(asset('/'), '/')) {
            $url .= '/entrance';
        }
        return redirect()->route('login')->with(['url' => $url, 'status' => 500]);
    }

}
