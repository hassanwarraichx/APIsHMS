<?php

namespace App\Http\Controllers\Specialization;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Specialization\SpecializationResource;
use App\Models\Specialization;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    public function index()
    {
        $specializations = Specialization::orderBy('name')->get();

        return ResponseHelper::success(
            SpecializationResource::collection($specializations),
            'List of specializations'
        );
    }
}
