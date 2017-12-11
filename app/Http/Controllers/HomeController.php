<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{

    public function showAppEntrance()
    {
        $app = app('Menu')->getAppData();
        return view('app_entrance', ['app' => $app]);
    }

    public function showDashBoard()
    {
        return view('dashboard');
    }

    public function showBlankPage()
    {
        return 'Nothing';
    }

}
