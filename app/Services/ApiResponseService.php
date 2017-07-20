<?php

namespace App\Services;

class ApiResponseService {

    /**
     * 生成基础错误响应
     * @return array
     */
    public function makeErrorResponse($message = '', $errorCode = 0, $stausCode = 500, $outerResponse = []) {
        $response = [
            'status' => -1,
            'message' => $message,
            'error_code' => $errorCode,
            'timestamp' => time()
        ];
        $response = array_collapse([$response, $outerResponse]);
        return response($response, $stausCode);
    }

    /**
     * 生成基础成功响应
     * @return array
     */
    public function makeSuccessResponse($message = '', $stausCode = 200, $outerResponse = []) {
        $response = [
            'status' => 1,
            'message' => $message,
            'timestamp' => time()
        ];
        $response = array_collapse([$response, $outerResponse]);
        return response($response, $stausCode);
    }

}
