@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mt-4">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Add User</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('add-user.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required>
                                    @error('username')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-sm" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary btn-sm" type="button" id="togglePassword">
                                            <i class="fa fa-eye" id="showIcon"></i>
                                            <i class="fa fa-eye-slash d-none" id="hideIcon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" required>
                                        <button class="btn btn-outline-secondary btn-sm" type="button" id="togglePasswordConfirm">
                                            <i class="fa fa-eye" id="showIconConfirm"></i>
                                            <i class="fa fa-eye-slash d-none" id="hideIconConfirm"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Buttons Row -->
                        <div class="row mt-2">
                            <div class="col-12 d-flex justify-content-start">
                                <button type="submit" class="btn btn-primary me-2">Add User</button>
                                <a href="{{ route('user-management.index') }}" class="btn btn-secondary">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const showIcon = document.getElementById('showIcon');
        const hideIcon = document.getElementById('hideIcon');

        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirm = document.getElementById('password_confirmation');
        const showIconConfirm = document.getElementById('showIconConfirm');
        const hideIconConfirm = document.getElementById('hideIconConfirm');

        togglePassword.addEventListener('click', function() {
            const type = password.type === 'password' ? 'text' : 'password';
            password.type = type;
            showIcon.classList.toggle('d-none');
            hideIcon.classList.toggle('d-none');
        });

        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirm.type === 'password' ? 'text' : 'password';
            passwordConfirm.type = type;
            showIconConfirm.classList.toggle('d-none');
            hideIconConfirm.classList.toggle('d-none');
        });
    });
</script>
@endsection