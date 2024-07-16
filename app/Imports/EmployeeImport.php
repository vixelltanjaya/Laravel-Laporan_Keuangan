<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class EmployeeImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) 
        {
            // Skip the header row
            if ($row[0] == 'username') {
                continue;
            }

            Employee::create([
                'username' => $row[0],
                'email' => $row[1],
                'department' => $row[2],
                'status' => $row[3],
            ]);
        }
    }
}
