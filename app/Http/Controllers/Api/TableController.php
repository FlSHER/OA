<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\HR\ShopCollection;
use App\Http\Resources\HR\StaffCollection;
use App\Models\HR\Shop;
use App\Models\HR\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TableController extends Controller
{
    public function getStaff(Request $request)
    {
        $response = app('Plugin')->oaTable($request, Staff::class);
        StaffCollection::withoutWrapping();
        $response['data'] = new StaffCollection($response['data']);
        return $response;
    }

    public function getShop(Request $request)
    {
        $response = app('Plugin')->oaTable($request, Shop::class);
        ShopCollection::withoutWrapping();
        $response['data'] = new ShopCollection($response['data']);
        return $response;
    }
}
