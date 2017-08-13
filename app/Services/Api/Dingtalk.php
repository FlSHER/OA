<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Api;

/**
 * Description of Dingtalk
 *
 * @author admin
 */
use Curl;
use Cache;

class Dingtalk {

    private $url; //服务端api- url
    private $corpId; //企业id
    private $corpSecret; //企业秘钥

    public function __construct() {
        $this->url = config('dingding.server_api');
        $this->corpId = config('dingding.CorpId');
        $this->corpSecret = config('dingding.CorpSecret');
    }

    /**
     * 获取js配置数据
     */
    public function getJsConfig() {
        $timeStamp = time();
        $nonceStr = config('dingding.nonceStr'); //签名的随机串
        $jsApiTicket = $this->getJsApiTicket();
        $url = $this->getCurrentUrl();
        $signature = $this->makeSignature($jsApiTicket, $nonceStr, $timeStamp, $url); //js-API签名
        $config = [
            'agentId' => config('dingding.agentId'), //微应用id
            'corpId' => config('dingding.CorpId'), //企业id
            'timeStamp' => $timeStamp, //生成签名的时间戳
            'nonceStr' => $nonceStr, //生成签名的随机串
            'signature' => $signature, //签名
        ];
        return $config;
    }

    /**
     * 免登
     * 通过CODE换取用户身份
     * @param type $code
     */
    public function passCodeGetUserInfo($code) {
        $message = [
            'access_token' => $this->getAccessToken(),
            'code' => $code
        ];
        $userInfo = Curl::build($this->url . 'user/getuserinfo')->sendMessage($message);
        return $userInfo;
    }

    /**
     * 获取jsapi-ticket
     */
    public function getJsApiTicket() {
//        if (Cache::has('jsApiTicket')) {
//            return Cache::get('jsApiTicket');
//        } else {
//            $response = $this->getJsApiTicketApi(); //生成jsApiTicket
//            $jsApiTicket = $response['ticket'];
//            $time = floor($response['expires_in'] / 60 - 1);
//            Cache::put('jsApiTicket', $jsApiTicket, $time);
//            return $jsApiTicket;
//        }
        $url = 'http://of.xigemall.com/api/get_dingtalk_js_api_ticket';
        return Curl::build($url)->get();
    }

    /**
     * api获取jsapi-ticket
     */
    private function getJsApiTicketApi() {
        $accessToken = $this->getAccessToken();
        $curl = Curl::build($this->url . 'get_jsapi_ticket?access_token=' . $accessToken);
        $response = $curl->get();
        return $response;
    }

    /**
     * 获取access_token
     */
    public function getAccessToken() {
//        if (Cache::has('accessToken')) {
//            return Cache::get('accessToken');
//        } else {
//            $accessToken = $this->getAccessTokenByApi();
//            Cache::put('accessToken', $accessToken, 119);
//            return $accessToken;
//        }
        $url = 'http://of.xigemall.com/api/get_dingtalk_access_token';
        return Curl::build($url)->get();
    }

    /**
     * api获取accessToken
     */
    private function getAccessTokenByApi() {
        $curl = Curl::build($this->url . 'gettoken');
        $message = ['corpid' => $this->corpId, 'corpsecret' => $this->corpSecret];
        $response = $curl->sendMessage($message);
        $token = $response['access_token'];
        return $token;
    }

    /**
     * 生成签名的url
     * @return type
     */
    private function getCurrentUrl() {
        $url = url()->full();
        $preg = '/\?_url=.*?&/';
        if (preg_match($preg, $url)) {
            $url = preg_replace($preg, '?', $url);
        } else {
            $preg = '/\?_url=.*/';
            $url = preg_replace($preg, '', $url);
        }
        return urldecode($url);
    }

    /**
     * jsApi 签名
     * @param type $jsapiTicket
     * @param type $nonceStr
     * @param type $timeStamp
     * @param type $url
     * @return type
     */
    private function makeSignature($jsapiTicket, $nonceStr, $timeStamp, $url) {
        $plain = 'jsapi_ticket=' . $jsapiTicket .
                '&noncestr=' . $nonceStr .
                '&timestamp=' . $timeStamp .
                '&url=' . $url;
        return sha1($plain);
    }

}
