@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="containter-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-4">
                        <div>
                            <h5 class="mb-0">Penggajian</h5>
                        </div>
                    </div>
                    @include('components.alert-danger-success')
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table id="payrollTable" >
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            NO
                                        </th>
                                        <th>
                                            Name
                                        </th>
                                        <th>
                                            Gaji
                                        </th>
                                        <th class="text-center">
                                            Diupdate Pada Tanggal
                                        </th>
                                        <th>
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $employee)
                                    <tr>
                                        <td class="text-center">
                                            <p>{{ $loop->iteration }}</p>
                                        </td>
                                        <td>
                                            <p>{{ $employee->username }}</p>
                                        </td>
                                        <td>
                                            <p>{{ $employee->formatted_gaji }}</p>
                                        </td>
                                        <td>
                                            <p class="text-center">{{ $employee->formatted_updated_at }}</p>
                                        </td>
                                        <td>
                                            <a href="" class="btn btn-link text-secondary font-weight-bold text-small" data-bs-toggle="modal" data-bs-target="#editModal{{$employee->id}}">
                                                <i class="ri-pencil-line"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- modal -->
@foreach ($employees as $employee)
<div class="modal fade" id="editModal{{$employee->id}}" tabindex="-1" aria-labelledby="editModalLabel{{$employee->id}}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{$employee->id}}">Add New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form content goes here -->
                <form action="{{route('payroll.store')}}" method="POST" >
                    @csrf
                    <div class="mb-3">
                        <label for="employee_name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="employee_name" name="employee_name" value="{{ $employee->username ?? '' }}" readonly>
                        <input type="hidden" id="employee_id" name="employee_id" value="{{ $employee->id ?? '' }}">
                    </div>

                    <div class="mb-3">
                        <label for="gaji" class="form-label">Gaji</label>
                        <input type="text" class="form-control" id="gaji" name="gaji" value="{{ old('gaji', $employee->gaji ?? $employee->honor) }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script>
    function formatNumber(value) {
        return new Intl.NumberFormat().format(value);
    }

    function removeFormatting(value) {
        return value.replace(/,/g, '');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const gajiInput = document.getElementById('gaji');

        // Format the number on load
        gajiInput.value = formatNumber(removeFormatting(gajiInput.value));

        // Remove formatting on focus
        gajiInput.addEventListener('focus', function() {
            gajiInput.value = removeFormatting(gajiInput.value);
        });

        // Reformat on blur
        gajiInput.addEventListener('blur', function() {
            gajiInput.value = formatNumber(gajiInput.value);
        });

        // Ensure the unformatted value is submitted
        gajiInput.addEventListener('change', function() {
            gajiInput.value = removeFormatting(gajiInput.value);
        });
    });
    $(document).ready(function() {
        $('#payrollTable').DataTable();
    });
</script>