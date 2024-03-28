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
            'Jabatan',
            'Terakhir_diupdate',
        ];
    }

    public function collection()
    {
        $employees = Employee::select('username', 'email', 'role', 'updated_at')->orderBy('updated_at', 'desc')->get();

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
                'Jabatan' => $employee->role,
                'Terakhir_diupdate' => $employee->updated_at,
            ];
        }

        // Return the modified collection
        return collect($modifiedCollection);
    }
}
