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
                        @if(isset($reportType) && $reportType === 'income')
                        @include('reporting.laporan-laba-rugi')
                        @elseif(isset($reportType) && $reportType === 'balance')
                        @include('reporting.laporan-posisi-keuangan')
                        @else
                        Laporan Arus Kas
                        @endif
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
            const {
                jsPDF
            } = window.jspdf;

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