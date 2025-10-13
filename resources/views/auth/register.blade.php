@extends('layouts.guest')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    .register-card {
        background: rgba(24,24,40,0.68);
        border-radius: 2rem;
        padding: 2.2rem 1.5rem 2rem 1.5rem;
        max-width: 370px;
        width: 100%;
        margin: 0 auto;
        box-shadow: 0 8px 32px #0003;
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
    }
    .register-label {
        color: #fff;
        font-size: 1rem;
        margin-bottom: 0.2rem;
        font-weight: 500;
    }
    .register-input {
        background: transparent;
        color: #fff;
        border: none;
        border-bottom: 2px solid #fff;
        border-radius: 0;
        font-size: 1.08rem;
        padding: 0.7rem 0.2rem 0.7rem 0.2rem;
        margin-bottom: 1.2rem;
        box-shadow: none;
    }
    .register-input:focus {
        background: transparent;
        color: #fff;
        border-bottom: 2px solid #ff2d2d;
        box-shadow: none;
    }
    .register-input::placeholder {
        color: #bbb;
        opacity: 1;
    }
    .register-select {
        background: transparent;
        color: #fff;
        border: none;
        border-bottom: 2px solid #fff;
        border-radius: 0;
        font-size: 1.08rem;
        padding: 0.7rem 0.2rem 0.7rem 0.2rem;
        margin-bottom: 1.2rem;
        box-shadow: none;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        background-size: 1rem;
        padding-right: 2.5rem;
    }
    .register-select:focus {
        background: transparent;
        color: #fff;
        border-bottom: 2px solid #ff2d2d;
        box-shadow: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        background-size: 1rem;
    }
    .register-select option {
        background: #181828;
        color: #fff;
        padding: 0.5rem;
        font-size: 1.08rem;
    }
    .register-select:not(:focus) {
        color: #fff !important;
    }
    .register-select:focus {
        color: #fff !important;
    }
    .register-btn {
        background: #ff2d2d;
        color: #fff;
        border: none;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 1.18rem;
        letter-spacing: 1px;
        box-shadow: 0 2px 8px #0002;
        padding: 0.95rem 0;
        margin-top: 0.7rem;
        margin-bottom: 0.7rem;
        width: 100%;
        transition: all .2s;
    }
    .register-btn:hover, .register-btn:focus {
        background: #d60000;
        color: #fff;
        box-shadow: 0 4px 16px #0003;
    }
    .register-links {
        font-size: 0.97rem;
        color: #fff;
        margin-bottom: 0.2rem;
        text-align: center;
    }
    .register-links a {
        color: #fff;
        text-decoration: underline;
        margin: 0 0.2rem;
    }
    .logo-row {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1.2rem;
        margin-bottom: 1.7rem;
    }
    .logo-row img {
        max-height: 48px;
        background: rgba(255,255,255,0.13);
        border-radius: 1rem;
        padding: 0.3rem 1.2rem;
    }
    @keyframes fadeSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: none; } }
    .fade-slide-up { animation: fadeSlideUp 0.9s cubic-bezier(.39,.575,.565,1) both; }
</style>
<div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center" style="padding-top: 40px; padding-bottom: 40px;">
    <div class="register-card d-flex flex-column align-items-center justify-content-center fade-slide-up">
        <div class="logo-row">
            <img src="{{ asset('assets/img/logo_futami.png') }}" alt="Futami Logo">
            <img src="{{ asset('assets/img/logo_limit_x.png') }}" alt="Limit X Logo">
        </div>
        <form method="POST" action="{{ route('register') }}" class="w-100 d-flex flex-column align-items-center">
            @csrf
            <div class="w-100" style="max-width: 270px;">
                <label for="name" class="register-label">Nama</label>
                <input id="name" type="text" name="name" class="form-control register-input @error('name') is-invalid @enderror" placeholder="Nama" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-100" style="max-width: 270px;">
                <label for="email" class="register-label">Email</label>
                <input id="email" type="email" name="email" class="form-control register-input @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required autocomplete="username">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-100" style="max-width: 270px;">
                <label for="role" class="register-label">Role</label>
                <select id="role" name="role" class="form-control register-select @error('role') is-invalid @enderror" required>
                    <option value="">Pilih Role</option>
                    <option value="technician" {{ old('role') == 'technician' ? 'selected' : '' }}>QA Lab. Technician</option>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>QA Staff</option>
                    <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>QA Supervisor</option>
                    <option value="guest" {{ old('role') == 'guest' ? 'selected' : '' }}>Guest User</option>
                </select>
                @error('role')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-100" style="max-width: 270px;">
                <label for="password" class="register-label">Password</label>
                <input id="password" type="password" name="password" class="form-control register-input @error('password') is-invalid @enderror" placeholder="Password" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-100" style="max-width: 270px;">
                <label for="password_confirmation" class="register-label">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control register-input @error('password_confirmation') is-invalid @enderror" placeholder="Konfirmasi Password" required autocomplete="new-password">
                @error('password_confirmation')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn register-btn">Register</button>
            <div class="register-links w-100 mt-2">
                <a href="{{ route('login') }}">Sudah punya akun?</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
</script>
@endsection
