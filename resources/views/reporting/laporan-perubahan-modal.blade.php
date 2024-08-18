<div id="headerTable" style="text-align: center;">
    <h2 class="mb-0">PT Maharani Putra Sejahtera</h2>
    <p class="mb-0" id="reportTitle">Laporan Perubahan Modal</p>
    <p class="mb-4" id="reportPeriod">
        Periode {{ $formattedEndDate }}
    </p>
</div>

<table id="tablePerubahanModal" class="table table-bordered">
    <tbody>
        <tr>
            <td><strong>Modal Pemilik</strong></td>
            <td></td>
            <td><strong>{{$modalPemilik }}</strong></td>
        </tr>
        <tr>
            <td>Laba</td>
            <td>{{  $laba }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Prive</td>
            <td>{{  $prive }}</td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Perubahan Modal</strong></td>
            <td></td>
            <td><strong>{{  $perubahanModal }}</strong></td>
        </tr>
        <tr>
            <td><strong>Modal Akhir</strong></td>
            <td></td>
            <td><strong>{{ $modalAkhir}}</strong></td>
        </tr>
    </tbody>
</table>