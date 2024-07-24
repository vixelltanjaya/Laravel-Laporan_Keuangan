<?php

namespace App\Http\Controllers;

use App\Models\CoaModel;
use App\Models\Division;
use Illuminate\Http\Request;

class ReportingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $division = Division::all();

        return view ('user-accounting.reporting', compact('division'));
    }
}
