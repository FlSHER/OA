<?php

declare(strict_types=1);

namespace Fisher\Amap\Admin\Controllers;

class HomeController
{
    public function index()
    {
        return trans('amap::messages.success');
    }
}
