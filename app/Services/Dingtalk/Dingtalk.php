<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Dingtalk;

/**
 * Description of Dingtalk
 *
 * @author admin
 */
use Curl;
use Cache;
use App\Models\HR\Staff;

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
        $jsApiTicket = Cache::remember('jsApiTicket', 115, function() {
                    $response = $this->getJsApiTicketApi(); //生成jsApiTicket
                    $jsApiTicket = $response['ticket'];
                    return $jsApiTicket;
                });
        return $jsApiTicket;
//        $url = 'http://of.xigemall.com/api/get_dingtalk_js_api_ticket';
//        return Curl::build($url)->get();
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
        $accessToken = Cache::remember('accessToken', 110, function() {
                    $accessToken = $this->getAccessTokenByApi();
                    return $accessToken;
                });
        return $accessToken;
//        $url = 'http://of.xigemall.com/api/get_dingtalk_access_token';
//        return Curl::build($url)->get();
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

    /**
     * 注册钉钉回调URL
     * @param type $callBackUrl
     * @param type $callBackTag
     * @return type
     */
    public function registerCallback($callBackUrl, $callBackTag) {
        $accessToken = $this->getAccessToken();
        $registerMessage = [
            'call_back_tag' => $callBackTag,
            'token' => config('dingding.token'),
            'aes_key' => config('dingding.AESKey'),
            'url' => $callBackUrl,
        ];
        $response = app('Curl')->setUrl(config('dingding.server_api') . 'call_back/register_call_back?access_token=' . $accessToken)->sendMessageByPost($registerMessage);
        if ($response['errcode'] == 71006) {
            $response = app('Curl')->setUrl(config('dingding.server_api') . 'call_back/update_call_back?access_token=' . $accessToken)->sendMessageByPost($registerMessage);
        }
        return $response;
    }

    public function startApprovalProcess($agentId, $processCode, $approvers_sn, $formData) {
        $dingId = app('CurrentUser')->dingding;
        $accessToken = $this->getAccessToken();
        $userInfo = app('Curl')->setUrl('https://oapi.dingtalk.com/user/get?access_token=' . $accessToken . '&userid=' . $dingId)->get();
        $departmentId = (string) $userInfo['department'][0];
        $approvers = $this->getApproversDingtalkId($approvers_sn);
        $realFormData = $this->makeRealFormData($formData);

        $req = new SmartworkBpmsProcessinstanceCreateRequest;
        $req->setAgentId($agentId);
        $req->setProcessCode($processCode);
        $req->setOriginatorUserId($dingId);
        $req->setDeptId($departmentId);
        $req->setApprovers($approvers);
        // $req->setCcList("");
        // $req->setCcPosition("FINISH");
        $req->setFormComponentValues(json_encode($realFormData));
        $dingTalk = new DingTalkClient;
        $response = $dingTalk->execute($req, $accessToken);
        dd($response);
    }

    protected function makeRealFormData($formData) {
        $response = [];
        foreach ($formData as $k => $v) {
            $response[] = ['name' => $k, 'value' => $v];
        }
        return $response;
    }

    protected function getApproversDingtalkId($approvers) {
        if (is_array($approvers)) {
            $response = [];
            foreach ($approvers as $v) {
                $response[] = Staff::find($v)->dingding;
            }
        } else {
            $response = Staff::find($approvers)->dingding;
        }
        return $response;
    }

}
