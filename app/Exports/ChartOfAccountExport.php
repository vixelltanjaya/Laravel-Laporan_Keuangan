<?php

namespace App\Exports;

use App\Models\CoaModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChartOfAccountExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return [
            'No',
            'Kode Akuntansi',
            'Nama Akun',
            'Akun Sign',
            'Tipe Akun',
            'Grup Akun',
        ];
    }

    public function collection()
    {
        $chartOfAccounts = CoaModel::select('account_id', 'account_name', 'account_sign', 'account_type','account_group')->orderBy('account_id', 'asc')->get();

        // array kosong tampung data
        $modifiedCollection = [];

        // inisialisasi nomor awal
        $number = 1;

        // loop tiap coa
        foreach ($chartOfAccounts as $coa) {
            $modifiedCollection[] = [
                'No' => $number++,
                'Kode_Akuntansi' => $coa->account_id,
                'Nama_Akun' => $coa->account_name,
                'Akun_Sign' => $coa->account_sign,
                'Tipe_Akun' => $coa->account_type,
                'Grup_Akun' => $coa->account_group,
            ];
        }

        // Return the modified collection
        return collect($modifiedCollection);
    }
}
