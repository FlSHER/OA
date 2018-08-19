<?php

$url = 'https://oapi.dingtalk.com/';
return [
    /**
     * 服务端接口地址
     */
    'server_api' => 'https://oapi.dingtalk.com/',
    /**
     * 企业ID
     */
    'CorpId' => env('DINGTALK_CORPID'),
    /**
     * 企业密钥
     */
    'CorpSecret' => env('DINGTALK_CORPSECRET'),
    /**
     * 微应用ID
     */
    'agentId' => '',
    /**
     * 签名随机字符串
     */
    'nonceStr' => env('DINGTALK_NONCESTR'),
    /**
     * 加解密token
     */
    'token' => env('DINGTALK_TOKEN'),
    /**
     * 加解密密钥，必须为43位
     */
    'AESKey' => env('DINGTALK_AESKEY'),

    /**
     * 消息通知
     */
    'message' => [
        //发送工作通知消息
        'jobNotification' => $url . 'topapi/message/corpconversation/asyncsend_v2',
    ],
    //待办事项
    'todo'=>[
        //发起待办
        'add'=>$url.'topapi/workrecord/add',
        //更新待办
        'update'=>$url.'topapi/workrecord/update',
    ]
];
