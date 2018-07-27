<?php

declare(strict_types=1);

namespace Fisher\Amap\Web\Controllers;

class HomeController
{
    public function index()
    {
        return view('amap::welcome');
    }
}
