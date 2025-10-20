@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="py-5 mx-auto" style="max-width: 480px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                                <img width="100px" src="{{ asset('assets/images/logo-icon.png') }}" alt="homepage" class="dark-logo" />

            <h4 class="mt-2 mb-0">Best Choice Payroll</h4>
            </div>
            <h5 class="mb-3">Create Admin Account</h5>
            <form method="POST" action="{{ route('register.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                    @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Create Account</button>
            </form>
            <div class="mt-3 text-center">
                <a href="{{ route('login') }}">Back to login</a>
            </div>
        </div>
    </div>
 </div>
@endsection


