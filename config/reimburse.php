<?php
/**
 *报销配置文件
 */

$reimburseUrl = env('REIMBURSE_URL');//报销
return [
    'url' => $reimburseUrl,
    'app_id'=>1,//应用ID
    'process_code'=>'PROC-RIYJG05W-K07XV0MWNFVAV3ADYNKU1-WDJY72KJ-32',//审批流的唯一码
    'manager_callback' => $reimburseUrl . 'api/callback/manager',//品牌副总单条审批回调地址
//    'manager_single_approve_callback'=>$reimburseUrl . 'api/manager-single-approve-callback',//品牌副总批量审批回调地址
    'manager_batch_approve_callback'=>$reimburseUrl .'api/callback/batch-callback',//品牌副总批量审批回调地址
];