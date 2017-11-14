<?php

if (!function_exists('source')) {

    function source($path, $secure = null)
    {
        if (file_exists($path)) {
            $path .= '?ver=' . date('md.H.i', filemtime($path));
        }
        return app('url')->asset($path, $secure);
    }

}

if (!function_exists('get_options')) {

    function get_options($table, $showColumn, $valueColumn = null, $where = [], $order = null)
    {
        if (is_object($table) || class_exists($table)) {
            $instance = is_object($table) ? $table : new $table;
            $data = $instance->where($where)
                ->when(!empty($order), function ($query) use ($order) {
                    foreach ($order as $column => $dir) {
                        $query->orderBy($column, $dir);
                    }
                    return $query;
                })->get()->all();
        } elseif (preg_match('/\./', $table)) {
            list($connection, $table) = explode('.', $table);
            $data = DB::connection($connection)->table($table)->where($where)
                ->when(!empty($order), function ($query) use ($order) {
                    foreach ($order as $column => $dir) {
                        $query->orderBy($column, $dir);
                    }
                    return $query;
                })->get()->all();
        } else {
            $data = DB::table($table)->where($where)
                ->when(!empty($order), function ($query) use ($order) {
                    foreach ($order as $column => $dir) {
                        $query->orderBy($column, $dir);
                    }
                    return $query;
                })->get()->all();
        }
        $options = '';
        foreach ($data as $v) {
            if (empty($valueColumn)) {
                $options .= "<option>{$v->$showColumn}</option>";
            } else {
                $options .= "<option value=\"{$v->$valueColumn}\">{$v->$showColumn}</option>";
            }
        }
        return $options;
    }

}

if (!function_exists('check_authority')) {

    function check_authority($authorityId)
    {
        return app('Authority')->checkAuthority($authorityId);
    }

}
