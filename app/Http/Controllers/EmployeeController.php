<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Imports\EmployeeImport;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\payroll;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $query = Employee::orderBy('id', 'asc');

        if (request()->has('search') && request('search') != '') {
            $search = request('search');
            $query->where(function ($query) use ($search) {
                $query->where('username', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $employees = $query->paginate(10);

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
            'email' => 'unique:employees,email',
            'departemen' => 'required',
            'status_pegawai' => 'required'
        ]);

        try {
            $employee = new Employee;

            $employee->username = $validateData['username'];
            $employee->email = $request->email ?? '';
            $employee->department = $validateData['departemen'];
            $employee->status = $validateData['status_pegawai'];

            $employee->save();

            return back()->with('berhasil', 'Data berhasil dimasukkan!');
        } catch (Exception $e) {
            return back()->withErrors(['gagal' => 'Data gagal dimasukkan: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $validatedData = $request->validate([
            'username' => 'required',
            'departemen' => 'required',
            'status_pegawai' => 'required',
        ]);

        $employee = Employee::findOrFail($id);

        $employee->username = $validatedData['username'];
        $employee->department = $validatedData['departemen'];
        $employee->status = $validatedData['status_pegawai'];

        Log::debug('Array ID ' . json_encode($id));
        Log::debug('Array emp ' . json_encode($employee));

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

    public function importAccount(Request $request)
    {
        Log::info('Apakah proses import masuk sini?');

        $request->validate([
            'import_employee' => 'required|file|mimes:xls,xlsx,csv',
        ]);

        if ($request->hasFile('import_employee')) {
            Log::info('File ditemukan: ' . $request->file('import_employee')->getClientOriginalName());

            try {
                Excel::import(new EmployeeImport, $request->file('import_employee'));
            } catch (Exception $e) {
                Log::error('Error during import: ' . $e->getMessage());
                return redirect()->back()->with('gagal', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
            }
        } else {
            Log::error('File tidak ditemukan dalam request');
            return redirect()->back()->with('gagal', 'File tidak ditemukan dalam request.');
        }

        return redirect()->back()->with('berhasil', 'File berhasil diunggah dan diproses.');
    }
}
