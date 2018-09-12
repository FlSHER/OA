<?php

namespace App\Http\Controllers\Api\Resources;

use App\Models\Authority;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\RbacCollection;

class RbacController extends Controller
{

    public function index()
    {
        $list = Authority::query()
            ->filterByQueryString()
            ->sortByQueryString()
            ->withPagination();
        
        if (isset($list['data'])) {
            $list['data'] = new RbacCollection($list['data']);

            return $list;
        }
        return new RbacCollection($list);
    }

}