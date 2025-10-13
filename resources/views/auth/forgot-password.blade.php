@extends('layouts.guest')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    .forgot-card {
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
    .forgot-label {
        color: #fff;
        font-size: 1rem;
        margin-bottom: 0.2rem;
        font-weight: 500;
    }
    .forgot-input {
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
    .forgot-input:focus {
        background: transparent;
        color: #fff;
        border-bottom: 2px solid #ff2d2d;
        box-shadow: none;
    }
    .forgot-input::placeholder {
        color: #bbb;
        opacity: 1;
    }
    .forgot-select {
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
    .forgot-select:focus {
        background: transparent;
        color: #fff;
        border-bottom: 2px solid #ff2d2d;
        box-shadow: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.7rem center;
        background-size: 1rem;
    }
    .forgot-select option {
        background: #181828;
        color: #fff;
        padding: 0.5rem;
        font-size: 1.08rem;
    }
    .forgot-select:not(:focus) {
        color: #fff !important;
    }
    .forgot-select:focus {
        color: #fff !important;
    }
    .forgot-btn {
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
    .forgot-btn:hover, .forgot-btn:focus {
        background: #d60000;
        color: #fff;
        box-shadow: 0 4px 16px #0003;
    }
    .forgot-info {
        color: #fff;
        font-size: 0.98rem;
        text-align: center;
        margin-bottom: 1.2rem;
    }
    .alert-success {
        background: rgba(40, 167, 69, 0.2);
        border: 1px solid rgba(40, 167, 69, 0.3);
        color: #28a745;
        border-radius: 0.5rem;
        padding: 0.75rem;
        font-size: 0.9rem;
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
    <div class="forgot-card d-flex flex-column align-items-center justify-content-center fade-slide-up">
        <div class="logo-row">
            <img src="{{ asset('assets/img/logo_futami.png') }}" alt="Futami Logo">
            <img src="{{ asset('assets/img/logo_limit_x.png') }}" alt="Limit X Logo">
        </div>
        <h3 class="mb-3 fw-bold text-center" style="color:#fff;">Lupa Password</h3>
        <div class="forgot-info">Masukkan <b>nama</b>, <b>email</b>, dan <b>role</b> Anda. Jika data cocok, Anda akan diarahkan untuk membuat password baru.</div>
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('password.email') }}" class="w-100 d-flex flex-column align-items-center">
            @csrf
            <div class="w-100" style="max-width: 270px;">
                <label for="name" class="forgot-label">Nama</label>
                <input id="name" type="text" name="name" class="form-control forgot-input @error('name') is-invalid @enderror" placeholder="Masukkan Nama" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-100" style="max-width: 270px;">
                <label for="email" class="forgot-label">Email</label>
                <input id="email" type="email" name="email" class="form-control forgot-input @error('email') is-invalid @enderror" placeholder="Masukkan Email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-100" style="max-width: 270px;">
                <label for="role" class="forgot-label">Role</label>
                <select id="role" name="role" class="form-control forgot-select @error('role') is-invalid @enderror" required>
                    <option value="">Pilih Role</option>
                    <option value="technician" {{ old('role') == 'technician' ? 'selected' : '' }}>QA Lab. Technician</option>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>QA Staff</option>
                    <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}>QA Supervisor</option>
                </select>
                @error('role')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn forgot-btn">Lanjutkan</button>
        </form>
    </div>
</div>
@endsection
