<?php

namespace App\Http\Controllers\HR;

use App\Models\HR\CostBrand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CostBrandController extends Controller
{
    public function index(Request $request)
    {
        $brandId = $request->get('brand_id');
        return CostBrand::whereHas('brands', function ($query) use ($brandId) {
            $query->where('id', $brandId);
        })->get();
    }
}
