<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Description of DatabaseManageService
 *
 * @author admin
 */
class DatabaseManageService {

    /**
     * 得到数据表
     * $data 数据类型 array
     */
    public static function getDataTable($data) {
        $table = '';
        if ($data['connection'] == 'mysql') {
            $table = self::getMysqlTable($data);
        } elseif ($data['connection'] == 'sqlsrv') {
            $table = self::getSqlsrvTable($data);
        }
        return $table;
    }

    /**
     * 得到mysql数据表
     * @param type $data
     */
    private static function getMysqlTable($data) {
        $pdo = self::getPdo($data);
        $table = $pdo->select('show tables');
        $table_arr = json_decode(json_encode($table), true);
        $table_array = [];
        foreach ($table_arr as $k => $v) {
            foreach ($v as $key => $val) {
                $table_array[] = $val;
            }
        }
        return json_encode($table_array);
    }

    /**
     * 得到sqlsrv数据表
     * @param type $data
     */
    private static function getSqlsrvTable($data) {
        $pdo = self::getPdo($data);
        $sql = "select * from sysobjects where xtype='U'";
        $table = $pdo->select($sql);
        return array_pluck($table, ['name']);
    }

    /**
     * 列表控件 获取数据的表的字段
     * @param Request $request
     */
    public static function getInternalDataField($data, $table) {
        $pdo = self::getPdo($data);
        if ($data['connection'] == 'mysql') {
            return self::getMysqlTableFields($pdo, $table);
        } elseif ($data['connection'] == 'sqlsrv') {
            return self::getSqlsrvTableFields($pdo, $table);
        }
    }

    /*
     * 得到mysql数据表的字段
     */

    private static function getMysqlTableFields($pdo, $table) {
        $fields = $pdo->select('show fields from ' . $table);
        return array_pluck($fields, ['Field']);
    }

    /**
     * 得到sqlsrv数据表的字段
     * @param type $pdo
     * @param type $table
     */
    private static function getSqlsrvTableFields($pdo, $table) {
        $sql = 'Select name from syscolumns Where ID=OBJECT_ID(\'' . $table . '\')';
        $fields = $pdo->select($sql);
        $fields = json_decode(json_encode($fields), true);
        return array_pluck($fields, ['name']);
    }

    /**
     * 获取数据的总条数
     * @param type $data
     * @param type $table
     * @param type $fields_arr
     * @return type
     */
    public static function getfieldsCount($data, $table, $fields_arr) {
        $pdo = self::getPdo($data);
        if ($data['connection'] == 'mysql') {
            $count = $pdo->table($table)->select($fields_arr)->count();
        } elseif ($data['connection'] == 'sqlsrv') {
            $obj = $pdo->select('select count(*)as count from ' . $table);
            $count = $obj[0]->count;
        }
        return $count;
    }

    /**
     * 获取字段的值
     * @param type $data 数据库配置信息
     * @param type $table 表名
     * @param type $fields_arr  字段数组
     */
    public static function getDataFieldsInfo($data, $table, $fields_arr, $start, $length) {
        $pdo = self::getPdo($data);
        if ($data['connection'] == 'mysql') {
            $info = $pdo->table($table)->select($fields_arr)->skip($start)->take($length)->get();
        } elseif ($data['connection'] == 'sqlsrv') {
            $fields_str = implode(',', $fields_arr);
            $info = $pdo->select('select top ' . $length . ' row_number() OVER(order by id desc)as id,' . $fields_str . ' from ' . $table . ' where id not in(select top ' . $start . ' id from ' . $table . ' order by id desc)');
            foreach ($info as $k => $v) {
                $newVal = [];
                foreach ($v as $key => $val) {
                    $encode = mb_detect_encoding($val, ['ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5']);
                    $newVal[] = iconv($encode, 'utf-8', $val);
                }
                $info[$k]= $newVal;
            }
        }

        return $info;
    }

    /**
     * 选择列表 分页处理
     * @param type $request
     * @param type $count
     */
    public static function getPage($request, $count) {
        $p = 1;
        if (isset($request->p)) {
            $p = intval($request->p);
        }
        if ($p < 1) {
            $p = 1;
        }

        $length = 10;

        $pages = ceil($count / $length);
        if ($p > $pages) {
            $p = $pages;
        }
        $start = ($p - 1) * $length;
        return [
            'p' => $p,
            'count' => $count,
            'pages' => $pages,
            'length' => $length,
            'start' => $start,
            'id' => $request->id,
            'table' => $request->table,
            'fields_arr' => $request->fields_arr,
        ];
    }

    public static function getPdo($data) {
//        $mysql = new \PDO('mysql:host=192.168.1.63:3306;dbname=workflow', 'liuyong', 'liuyong');//mysql
//        $mysql = mysqli_connect('192.168.1.6', 'root', 'root', 'weixin', '3306');//mysql
//        $sqlsrv = new \PDO('odbc:Driver={SQL Server};Server=125.64.17.214;Database=firstframe215', 'sa', 'hjl123456'); //sqlsrv
//        return DB::connection()->setPdo($mysql);
        if ($data['connection'] == 'mysql') {
            $mysql = new \PDO($data['connection'] . ':host=' . $data['host'] . ':' . $data['port'] . ';dbname=' . $data['database'], $data['username'], $data['password']);
            return DB::connection()->setPdo($mysql);
        } elseif ($data['connection'] == 'sqlsrv') {
            $sqlsrv = new \PDO('odbc:Driver={SQL Server};Server=' . $data['host'] . ';Database=' . $data['database'] . '', $data['username'], $data['password']); //sqlsrv
            return DB::connection()->setPdo($sqlsrv);
        }
    }

}
