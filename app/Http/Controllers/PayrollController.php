<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $employees = Employee::joinPayroll()->get()->map(function ($employee) {
            $employee->formatted_gaji = number_format($employee->gaji, 0, ',', '.');
            $employee->formatted_updated_at = Carbon::parse($employee->updated_at)->format('d-m-Y');
            return $employee;
        });

        return view('user-admin.payroll', compact('employees'));
    }



    public function store(Request $request)
    {
        Log::debug('cek request' .json_encode($request->all()));
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'gaji' => 'required|numeric',
        ]);

        $payroll = new Payroll();
        $payroll->employees_id = $request->employee_id;
        $payroll->gaji = $request->gaji;
        $payroll->save();

        Log::debug('gaji' .$payroll);
        return redirect()->back()->with('berhasil', 'Payroll information berhasil ditambah.');
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
