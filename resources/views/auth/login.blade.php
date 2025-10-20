@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="py-5 mx-auto" style="max-width: 480px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                                <img width="100px" src="{{ asset('assets/logo-word.png') }}" alt="homepage" class="dark-logo" />

            <h4 class="mt-2 mb-0">Best Choice Payroll</h4>
            </div>
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <h5 class="mb-3">Admin Login</h5>
            <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100" id="loginSubmitBtn">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <span class="submit-text">Login</span>
            </button>
            </form>
            <div class="mt-3 text-center">
            <a href="{{ route('register') }}">Create an admin account</a>
            </div>
        </div>
    </div>
 </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var loginForm = document.getElementById('loginForm');
    var loginBtn = document.getElementById('loginSubmitBtn');
    var spinner = loginBtn ? loginBtn.querySelector('.spinner-border') : null;
    var text = loginBtn ? loginBtn.querySelector('.submit-text') : null;
    if (loginForm && loginBtn && spinner && text) {
        loginForm.addEventListener('submit', function () {
            loginBtn.classList.add('disabled');
            spinner.classList.remove('d-none');
            text.textContent = 'Logging in...';
        });
    }
});
</script>
@endsection


