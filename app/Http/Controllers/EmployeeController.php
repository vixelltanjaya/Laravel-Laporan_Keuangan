<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use Illuminate\Http\Request;
use App\Models\Employee;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $employees = Employee::orderBy('id', 'asc');

        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $employees->where(function ($query) use ($search) {
                $query->where('username', 'like', '%' . $search . '%')
                ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        $employees = $employees->simplePaginate(10);

        return view('user-admin.employee', [
            'employees' => $employees
        ]);

        // $employee = Employee::latest();

        // if (request('search')){
        //     $employee->where('username','like','%' . request('search') . '%');
        // }

        // return view ('user-admin/employee', [
        //     'employees' => Employee::orderBy('id','asc')->get()]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validateData = $request->validate([
            'username' => 'required',
            'email' => 'required|email|unique:employees,email',
            'role' => 'required',
        ]);

        $employee = new Employee;

        $employee->username = $validateData['username'];
        $employee->email = $validateData['email'];
        $employee->role = $validateData['role'];

        $employee->save();

        return back()->with('berhasil', 'Data berhasil dimasukkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validatedData = $request->validate([
            'username' => 'required',
            'email' => 'required|email|unique:employees,email',
            'role' => 'required',
        ]);

        $employee = Employee::findOrFail($id);

        $employee->username = $validatedData['username'];
        $employee->email = $validatedData['email'];
        $employee->role = $validatedData['role'];

        $employee->save();

        return back()->with('berhasil', 'Data terupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Employee::find($id)->delete();

        return back()->with('berhasil', 'Data berhasil dihapus');
    }

    public function exportexcel()
    {
        return Excel::download(new EmployeeExport, 'data_pegawai.xlsx');
    }
}
