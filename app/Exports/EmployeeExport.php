<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Email',
            'Departemen',
            'Status',
            'Terakhir_diupdate',
        ];
    }

    public function collection()
    {
        $employees = Employee::select('username', 'email', 'department', 'status','updated_at')->orderBy('updated_at', 'desc')->get();

        // array kosong tampung data
        $modifiedCollection = [];

        // inisialisasi nomor awal
        $number = 1;

        // loop tiap emps
        foreach ($employees as $employee) {
            $modifiedCollection[] = [
                'No' => $number++,
                'Nama' => $employee->username,
                'Email' => $employee->email,
                'Departemen' => $employee->department,
                'Status' => $employee->status,
                'Terakhir_diupdate' => $employee->updated_at,
            ];
        }

        // Return the modified collection
        return collect($modifiedCollection);
    }
}
