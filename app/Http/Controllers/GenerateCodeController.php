<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GenerateCodeService;

class GenerateCodeController extends Controller
{
    protected $generateCodeService;

    public function __construct(GenerateCodeService $generateCodeService) {
        $this->generateCodeService = $generateCodeService;
    }

    public function generateRefCode(Request $request) {
        $prefix_code = $request->input('prefix_code');
        $newRefCode = $this->generateCodeService->generateRefCode($prefix_code);

        return response()->json(['ref' => $newRefCode]);
    }
}
