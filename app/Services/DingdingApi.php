<?php

/**
 * Curl类
 * create by Fisher 2016/9/5 <fisher9389@sina.com>
 */

namespace App\Services;

use App\Services\CurlService as Curl;
use Cache;

class DingdingApi {

    private $serverApi;
    private $CorpId;
    private $CorpSecret;

    public function __construct() {
        $this->serverApi = config('dingding.server_api');
        $this->CorpId = config('dingding.CorpId');
        $this->CorpSecret = config('dingding.CorpSecret');
    }

    /**
     * 发送大爱通知
     * @param type $userid
     * @param type $content
     * @return type
     */
    public function sendViolationMessage($userid, $content) {
        return $this->sendMessage($userid, $content, 'violation');
    }

    /**
     * 钉钉通知
     * @param type $userid
     * @param type $content
     * @param type $agentName
     * @return type
     */
    public function sendMessage($userid, $content, $agentName = null) {
        $headers = [
            'Content-Type: application/json; charset=utf-8'
        ];
        $agentId = empty($agentName) ? config('dingding.agentId') : config('dingding.agentId.' . $agentName);
        $curl = Curl::build($this->serverApi . 'message/send?access_token=' . $this->getAccessToken())->setHeader($headers);
        $message = [
            'touser' => $userid,
            'agentid' => $agentId,
            'msgtype' => 'text',
            'text' => [
                'content' => $content,
            ],
        ];
        return $curl->sendMessageByPost($message);
    }

    /* |---------------------------------| */
    /* |------------ private ------------| */
    /* |---------------------------------| */

    private function getAccessToken() {
        if (Cache::has('AccessToken')) {
            return Cache::get('AccessToken');
        } else {
            $accessToken = $this->getAccessTokenByApi();
            Cache::put('AccessToken', $accessToken, 119);
            return $accessToken;
        }
    }

    private function getAccessTokenByApi() {
        $curl = Curl::build($this->serverApi . 'gettoken');
        $message = ['corpid' => $this->CorpId, 'corpsecret' => $this->CorpSecret];
        $responses = $curl->sendMessage($message);
        $response = json_decode($responses);
        $token = $response->access_token;
        return $token;
    }

}
