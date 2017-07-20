<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services\Workflow;

/**
 * 分页
 * Description of PageService
 *
 * @author admin
 */
class PageService {

    /**
     * 分页
     * @param type $request
     */
    public function getPage($request) {
        $p = 1;
        if (isset($request['p'])) {
            $p = intval($request['p']);
        }
        if ($p < 1) {
            $p = 1;
        }
        $length = 100;
        if (isset($request['length'])) {
            $length = intval($request['length']);
        }
        $start = ($p - 1) * $length;
        return [
            'p' => $p,
            'length' => $length,
            'start' => $start
        ];
    }

}
