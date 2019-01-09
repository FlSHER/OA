<?php

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;

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

if (!function_exists('createRequest')) {

    function createRequest($url = '', $method = 'POST', $params = array(), $original = 1)
    {
        $request = Request::create($url, $method, $params);
        $request->headers->add([
            'Accept' => 'application/json', 
        ]);
        app()->instance(Request::class, $request);

        $response = Route::dispatch($request);
        
        return $original ? $response->original : $response;
    }
}

if (!function_exists('unique_validator')) {

    function unique_validator(string $table, bool $ignore = true, bool $hasDel = false)
    {
        $rule = Rule::unique($table);

        if ($ignore) {
            if ($table === 'staff') {
                $rule->ignore(request()->staff_sn, 'staff_sn');
            } else {
                $rule->ignore(request()->id);
            }
        }
        if ($hasDel === false) {
            $rule->where(function ($query) {
                $query->whereNull('deleted_at');
            });
        }

        return $rule;
    }
}
if (!function_exists('array_to_tree')) {
    function array_to_tree($list, $pk = 'id', $pid = 'parent_id', $child = '_children', $root = 0)
    {
        $tree = [];
        if (is_array($list)) {
            $refer = [];
            foreach ($list as $key => $data) {
                $list[$key][$child] = [];
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
}