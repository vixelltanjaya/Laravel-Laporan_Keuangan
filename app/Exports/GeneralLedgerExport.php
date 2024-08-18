<?php

namespace App\Exports;

use Illuminate\Support\Collection as SupportCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class GeneralLedgerExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    protected $monthYear;
    protected $processedAccounts;
    protected $formattedDate;

    public function __construct(SupportCollection $processedAccounts, $monthYear, $formattedDate)
    {
        $this->processedAccounts = $processedAccounts;
        $this->monthYear = $monthYear;
        $this->formattedDate = $formattedDate;
    }

    public function collection()
    {
        $data = collect();

        foreach ($this->processedAccounts as $accountId => $entries) {
            $data->push([
                'Nama Akun: ' . $entries->first()->account_name,
                '',
                '',
                '',
                '',
                'Kode Akun: ' . $accountId,
            ]);

            $data->push([
                'Tanggal',
                'Keterangan',
                'Nomor Bukti',
                'Debit',
                'Kredit',
                'Amount',
            ]);

            foreach ($entries as $entry) {
                $data->push([
                    $entry->formattedDateTrx,
                    $entry->description,
                    $entry->evidence_code,
                    $entry->debit ? number_format($entry->debit, 2) : '',
                    $entry->credit ? number_format($entry->credit, 2) : '',
                    number_format($entry->amount, 2),
                ]);
            }

            // Add a total row for the account
            $totalAmount = $entries->sum('amount');
            $data->push([
                '',
                '',
                '',
                '',
                'Total:',
                number_format($totalAmount, 2),
            ]);

            $data->push(['', '', '', '', '', '']);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            ['Buku Besar'],
            ['Detail Transaksi'],
            ['Periode: ' . $this->formattedDate],
        ];
    }

    public function title(): string
    {
        return 'Laporan Buku Besar';
    }

    public function startCell(): string
    {
        return 'A5';
    }
}
