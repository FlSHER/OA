<?php

namespace App\Http\Controllers\Api\HR;

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
