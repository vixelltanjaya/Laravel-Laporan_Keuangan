@extends('layouts.user_type.auth')

@section('content')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
  <div class="container-fluid py-4">
    <div class="row">
        <div class="nav-item d-flex justify-content-end">
          <a href="/importexcel" target="_blank" class="btn btn-primary active mb-0 me-2 text-white" role="button" aria-pressed="true">
            <i class="fas fa-download me-1"></i>Download</a>
          <a href="/importexcel" target="_blank" class="btn btn-primary active mb-0 text-white" role="button" aria-pressed="true">Import Data</a>
        </div>
        <div class="card mb-4">
          <div class="card-header pb-0">
            <h6>Chart Of Account</h6>
            <div class="col-md-8 ms-md-1 pe-md-1 align-items-center">
              <form action="/posts" class="form-inline">
                <div class="input-group mb-3">
                  <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
                  <button class="btn btn-primary active mb-0 text-white my-2 my-sm-0" type="submit">Search</button>
                </div>
              </form>
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kode Akuntansi</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Akun</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipe Akun</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Saldo</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                    <th class="text-secondary opacity-7"></th>
                  </tr>
                </thead>
                <tbody>

                  <tr>
                    <td style="padding-left: 25px;">
                      <p>1-110</p>
                    </td>
                    <td>
                      <p>Beban Barang dengan termin 5 tahun (test)</p>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <p> Expense </p>
                    </td>
                    <td class="align-middle text-center">
                      <p>test</p>
                    </td>
                    <td class="align-middle text-center">
                      <a href="javascript:;" class="mx-1" id="openEditModal" data-toggle="modal" data-target="#editUserModal" data-original-title="Edit user">
                        <i class="fas fa-user-edit text-secondary"></i>
                      </a>
                      <a href="javascript:;" class="mx-1" data-toggle="tooltip" data-original-title="Edit user">
                        <i class="fa fa-trash-o text-secondary"></i>
                      </a>
                      <a href="javascript:;" class="mx-1" data-toggle="tooltip" data-original-title="Edit user">
                        <i class="fa fa-plus-circle text-secondary"></i>
                      </a>
                    </td>
                  </tr>

                </tbody>
              </table>
            </div>
          </div>
        </div>
    </div>
  </div>

  <!-- Modal Section -->


</main>


@endsection