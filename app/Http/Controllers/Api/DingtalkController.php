<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\App;
use ApiResponse;
use DB;

class DingtalkController extends Controller
{

    public function getAccessToken()
    {
        return ApiResponse::makeSuccessResponse(app('Dingtalk')->getAccessToken(), 200);
    }

    public function getJsApiTicket()
    {
        return ApiResponse::makeSuccessResponse(app('Dingtalk')->getJsApiTicket(), 200);
    }

    public function getDingtalkConfig(Request $request)
    {
        return ApiResponse::makeSuccessResponse(app('Dingtalk')->getJsConfig($request->agent_id), 200);
    }

    public function startApproval(Request $request)
    {
        try {
            $appId = $request->user()->token()->client_id;
            $processCode = $request->process_code;
            $approvers = $request->approvers;
            $formData = $request->form_data;
            $callback = $request->callback_url;
            $initiatorSn = $request->has('initiator_sn') ? $request->initiator_sn : null;
            $processInstanceId = app('Dingtalk')->startApprovalAndRecord($appId, $processCode, $approvers, $formData, $callback, $initiatorSn);
            return app('ApiResponse')->makeSuccessResponse($processInstanceId, 200);
        } catch (HttpException $e) {
            return app('ApiResponse')->makeErrorResponse($e->getMessage(), 501, $e->getStatusCode());
        }
    }

    public function approvalCallback(Request $request)
    {
        DB::table('test')->insert(['time' => date('Y-m-d H:i:s'), 'remark' => json_encode($request->all())]);
        $requestMsg = app('Dingtalk')->decryptMsg($request->signature, $request->timestamp, $request->nonce, $request->encrypt);
        if ($requestMsg == ['EventType' => 'check_url']) {
            $responseMsg = app('Dingtalk')->encryptMsg('success', $request->timestamp, $request->nonce);
        } else {
            DB::table('test')->insert(['time' => date('Y-m-d H:i:s'), 'remark' => json_encode($requestMsg)]);
            $callbackUrl = DB::table('dingtalk_approval_process')->where('process_instance_id', $requestMsg['processInstanceId'])->value('callback_url');
            $appResponse = app('Curl')->setUrl($callbackUrl)->sendMessageByPost($requestMsg);
            DB::table('test')->insert(['time' => date('Y-m-d H:i:s'), 'remark' => (string)$appResponse]);
            $responseMsg = app('Dingtalk')->encryptMsg('success', $request->timestamp, $request->nonce);
        }
        return $responseMsg;
    }

    public function registerApprovalCallback()
    {
        $response = app('Dingtalk')->registerCallback(env('APP_URL') . 'api/dingtalk/approval_callback', ['bpms_task_change', 'bpms_instance_change']);
        return $response;
    }

}
