<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cache;

class CacheController extends Controller {

    public function flushCache(Request $request) {
        $tags = $request->has('tag') ? $request->tag : [];
        Cache::tags($tags)->flush();
        return redirect()->back();
    }

}
