<?php

namespace App\Imports;

use App\Models\CoaModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class ChartOfAccountImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            // Skip header row
            if ($row[0] == 'account_id') {
                continue;
            }
            // jika ada data kosong tidak terisi dengan null
            $accountId = $row[0] ?? '';
            $accountName = $row[1] ?? '';
            $accountSign = $row[2] ?? '';
            $accountType = $row[3] ?? '';
            $accountGroup = $row[4] ?? '';

            // buat coa instance dan disimpan ke db
            CoaModel::create([
                'account_id' => $accountId,
                'account_name' => $accountName,
                'account_sign' => $accountSign,
                'account_type' => $accountType,
                'account_group' => $accountGroup,
            ]);
        }
    }
}
