<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ApiResponse;

class DingtalkController extends Controller {

    public function getAccessToken() {
        return ApiResponse::makeSuccessResponse(app('Dingtalk')->getAccessToken(), 200);
    }

    public function getJsApiTicket() {
        return ApiResponse::makeSuccessResponse(app('Dingtalk')->getJsApiTicket(), 200);
    }

    public function startApproval(Request $request) {
        app('Dingtalk')->registerCallback();
    }

    public function approvalCallback(Request $request) {
        $msg = "";
        $crypt = new DingtalkCrypt(config('dingding.token'), config('dingding.AESKey'), config('dingding.CorpId'));
        $requestErrCode = $crypt->DecryptMsg($request->signature, $request->timestamp, $request->nonce, $request->encrypt, $msg);
        $requestMsg = json_decode($msg, true);
        if ($requestErrCode == 0 && $requestMsg['EventType'] !== 'check_url') {
            $remark = [
                'errCode' => $requestErrCode,
                'message' => $requestMsg,
            ];
            DB::table('test')->insert(['time' => date('Y-m-d H:i:s'), 'remark' => json_encode($remark)]);
        }
        $crypt->EncryptMsg('success', $request->timestamp, $request->nonce, $msg);
        return $msg;
    }

}
