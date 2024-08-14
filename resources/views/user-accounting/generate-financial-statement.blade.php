@extends('layouts.user_type.auth')

@section('content')
<main class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <a href="{{ url()->previous() }}" class="btn btn-default">Batal</a>
            <div class="card">
                <div class="card-body">
                    @if(isset($reportType) && $reportType === 'income')
                    @include('reporting.print-laba-rugi')
                    @include('reporting.laporan-laba-rugi')
                    @elseif(isset($reportType) && $reportType === 'balance')
                    @include('reporting.print-posisi-keuangan')
                    @include('reporting.laporan-posisi-keuangan')
                    @else
                    @include('reporting.laporan-perubahan-modal')
                    @endif
                </div>
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