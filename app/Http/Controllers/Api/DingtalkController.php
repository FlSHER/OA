<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\App;
use ApiResponse;
use DB;

class DingtalkController extends Controller {

    public function getAccessToken() {
        return ApiResponse::makeSuccessResponse(app('Dingtalk')->getAccessToken(), 200);
    }

    public function getJsApiTicket() {
        return ApiResponse::makeSuccessResponse(app('Dingtalk')->getJsApiTicket(), 200);
    }

    public function startApproval(Request $request) {
        try {
            $agentId = App::find($request->app_id)->agent_id;
            $processCode = $request->process_code;
            $approvers = $request->approvers;
            $formData = $request->form_data;
            $response = app('Dingtalk')->startApprovalProcess($agentId, $processCode, $approvers, $formData);
            if (!empty($response->result) && $response->result->ding_open_errcode == 0) {
                DB::table('dingtalk_approval_process')->insert([
                    'app_id' => $request->app_id,
                    'process_instance_id' => $response->result->process_instance_id,
                    'callback_url' => $request->callback_url,
                ]);
                return app('ApiResponse')->makeSuccessResponse($response->result->process_instance_id, 200);
            } else {
                return app('ApiResponse')->makeErrorResponse($response, 500);
            }
        } catch (HttpException $e) {
            return app('ApiResponse')->makeErrorResponse($e->getMessage(), 501, $e->getStatusCode());
        }
    }

    public function approvalCallback(Request $request) {
        $requestMsg = app('Dingtalk')->decryptMsg($request->signature, $request->timestamp, $request->nonce, $request->encrypt);
        if ($requestMsg['EventType'] == 'bpms_task_change' || $requestMsg['type'] == 'terminate') {
            DB::table('test')->insert(['time' => date('Y-m-d H:i:s'), 'remark' => json_encode($requestMsg)]);
            $callbackUrl = DB::table('dingtalk_approval_process')->where('process_instance_id', $requestMsg['processInstanceId'])->value('callback_url');
            $appResponse = app('Curl')->setUrl($callbackUrl)->sendMessageByPost($requestMsg);
        }
        $responseMsg = app('Dingtalk')->encryptMsg('success', $request->timestamp, $request->nonce);
        return $responseMsg;
    }

    public function registerApprovalCallback() {
        $response = app('Dingtalk')->registerCallback('http://of.xigemall.com/api/dingtalk/approval_callback', ['bpms_task_change', 'bpms_instance_change']);
        return $response;
    }

}
