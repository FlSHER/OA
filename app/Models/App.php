<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class App extends Model {
    /* ----- 定义关联Start ----- */

    public function auth_code() { //授权码
        return $this->belongsToMany('App\Models\HR\Staff', 'app_auth_code', 'app_id', 'staff_sn');
    }

    public function app_token() { //访问token
        return $this->belongsToMany('App\Models\HR\Staff', 'app_token', 'app_id', 'staff_sn');
    }

    /* ----- 定义关联End ----- */

    /**
     * 验证app ticket
     * @param array $passport {id,secret,timestamp}
     * @param type $clientIp
     * @return boolean
     */
    public static function checkAppTicket(array $passport, $clientIp) {
        $app = self::find($passport['app_id']);
        if (!empty($app)) {
            $secret = hash('sha256', $app->app_ticket . $passport['timestamp']);
            if ($passport['secret'] == $secret && $app->client_ip == $clientIp) {
                return true;
            }
        }
        return false;
    }

}
