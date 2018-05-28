<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ResponseController extends Controller {

    public function showErrorPage() {
        return request()->all();
    }

}
