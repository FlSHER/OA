<?php

namespace App\Http\Controllers\Api\Resources;

use App\Models\I\Education;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BasicInfoController extends Controller
{
    public function indexEducation()
    {
        return Education::all();
    }
}
