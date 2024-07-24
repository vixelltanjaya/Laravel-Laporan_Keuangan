@extends('layouts.user_type.auth')

@section('content')
<main class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <a href="{{ url()->previous() }}" class="btn btn-default">Batal</a>
            <div class="card">
                <div class="card-header pb-0 px-3">
                    <div class="text-left mb-4">
                        <button id="printButton" class="btn btn-dark">
                            <i class="fas fa-print"></i> Print to PDF
                        </button>
                    </div>
                </div>
                <div class="card-body pt-4 p-3 text-center">
                    <div class="text-center mb-4">
                        <h6 class="mb-0">PT. Maharani Putra Sejahtera</h6>
                        <p class="mb-0" id="reportTitle">
                            @if(isset($reportType) && $reportType === 'income')
                            Laporan Laba Rugi
                            @elseif(isset($reportType) && $reportType === 'balance')
                            Laporan Posisi Keuangan
                            @else
                            Laporan Arus Kas
                            @endif
                        </p>
                        <p class="mb-0" id="reportPeriod">
                            Periode
                            {{ request('transaction_month_start') ? date('F Y', strtotime(request('transaction_month_start') . '-01')) : '' }}
                            -
                            {{ request('transaction_month_end') ? date('F Y', strtotime(request('transaction_month_end') . '-01')) : '' }}
                        </p>
                    </div>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Perhitungan</strong></td>
                                <td>Jumlah</td>
                                <td></td>
                            </tr>
                            @foreach($incomeStatement['revenue'] as $revenue)
                            <tr>
                                <td>{{ $revenue['description'] }}</td>
                                <td>{{ $revenue['amount'] }}</td>
                                <td></td>
                            </tr>
                            @endforeach
                            <tr>
                                <td><strong>Total Pendapatan</strong></td>
                                <td>{{ $incomeStatement['total_revenue'] }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><strong>Beban</strong></td>
                                <td>Jumlah</td>
                                <td></td>
                            </tr>
                            @foreach($incomeStatement['expenses'] as $expense)
                            <tr>
                                <td>{{ $expense['description'] }}</td>
                                <td>{{ $expense['amount'] }}</td>
                                <td></td>
                            </tr>
                            @endforeach
                            <tr>
                                <td><strong>Total Beban</strong></td>
                                <td>{{ $incomeStatement['total_expenses'] }}</td>
                                <td></td>
                            </tr>
                            <!-- Laba Bersih Section -->
                            <tr>
                                <td><strong>Laba Bersih</strong></td>
                                <td>{{ $incomeStatement['net_income'] }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

<!-- Script for printing the data to PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('printButton').addEventListener('click', () => {
            const { jsPDF } = window.jspdf;

            const doc = new jsPDF();
            const element = document.querySelector('.card-body');

            doc.html(element, {
                callback: function(doc) {
                    doc.save('Laporan_Laba_Rugi.pdf');
                },
                x: 10,
                y: 10
            });
        });
    });
</script>
