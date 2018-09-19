<?php
/**
 *报销配置文件
 */

$reimburseUrl = env('REIMBURSE_URL');//报销
return [
    'url' => $reimburseUrl,
    'app_id'=>1,//应用ID
    'single_process_code'=>'PROC-GLYJ5N2V-E11VUX0YRK67A1WOOODU2-G8JBUYGJ-1',//单条审批流的唯一码
    'batch_process_code'=>'PROC-RIYJG05W-K07XV0MWNFVAV3ADYNKU1-WDJY72KJ-32',//批量审批流的唯一码
    'manager_callback' => $reimburseUrl . 'api/callback/manager',//品牌副总单条审批回调地址
    'manager_batch_approve_callback'=>$reimburseUrl .'api/callback/batch-callback',//品牌副总批量审批回调地址
];