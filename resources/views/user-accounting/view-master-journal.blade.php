@extends('layouts.user_type.auth')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between my-3">
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-default"> <i class="fas fa-angle-left me-1">Back</i></a>
        <div class="card border-none">
            <div class="card-body">
                <h4 class="mb-4">Detail Master Journal</h4>
                <div class="form-group col-md-6 mb-3">
                    <label class="h6 mb-0">Kode Transaksi :</label>
                    <label class="h6 font-weight-lighter mb-0">{{ $masterJournal->code }}</label>
                </div>

                <div class="form-group col-md-6 mb-3">
                    <label class="h6 mb-0">Deskripsi :</label>
                    <label class="h6 font-weight-lighter mb-0">{{ $masterJournal->description }}</label>
                </div>
                <div class="form-group col-md-6 mb-4">
                    <label class="h6 mb-0">Kode Bukti Transaksi: </label>
                    <label class="h6 font-weight-lighter mb-0">{{ $EvidenceCode->firstWhere('id', $masterJournal->evidence_id)->prefix_code . ' - ' . $EvidenceCode->firstWhere('id', $masterJournal->evidence_id)->code_title ?? '' }}</label>
                </div>
                <div id="DetailJournals_" class="detail-journal">
                    <hr>
                    <div>
                    </div>
                    <div class="form-group row font-weight-bold">
                        <div class="col-md-6">
                            <span>Keterangan</span>
                        </div>
                        <div class="col-md-3">
                            <span>Debit</span>
                        </div>
                        <div class="col-md-3">
                            <span>Kredit</span>
                        </div>
                    </div>
                    <div class="journal-entries">
                        @if ($detailJournal && $detailJournal->count())
                        @foreach($detailJournal as $index => $detail)
                        <div class="form-group row text-center {{ $detail->account_position == 'credit' ? 'credit-row' : '' }}">
                            <div class=" col-md-6">
                            <input type="text" class="form-control-plaintext" name="noAccount[]" value="{{ $detail->gl_account }} - {{ $detail->account_name}}" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control-plaintext" name="accountSign[]" value="{{ $detail->account_position == 'debit' ? 'Rp.XXX' : '' }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control-plaintext" name="accountSign[]" value="{{ $detail->account_position == 'credit' ? 'Rp.XXX' : '' }}" readonly>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p>No journal details available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

<style>
    .credit-row {
        padding-left: 35px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>