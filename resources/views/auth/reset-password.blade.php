@extends('layouts.guest')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    .reset-card {
        background: rgba(24,24,40,0.72);
        border-radius: 1.5rem;
        padding: 2.2rem 1.5rem 2rem 1.5rem;
        max-width: 350px;
        width: 100%;
        margin: 0 auto;
        box-shadow: 0 2px 16px #0004;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .reset-label {
        color: #fff;
        font-size: 1rem;
        margin-bottom: 0.2rem;
        font-weight: 500;
    }
    .reset-input {
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
    .reset-input:focus {
        background: transparent;
        color: #fff;
        border-bottom: 2px solid #ff2d2d;
        box-shadow: none;
    }
    .reset-input::placeholder {
        color: #bbb;
        opacity: 1;
    }
    .alert-success {
        background: rgba(40, 167, 69, 0.2);
        border: 1px solid rgba(40, 167, 69, 0.3);
        color: #28a745;
        border-radius: 0.5rem;
        padding: 0.75rem;
        font-size: 0.9rem;
    }
    .reset-btn {
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
    .reset-btn:hover, .reset-btn:focus {
        background: #d60000;
        color: #fff;
        box-shadow: 0 4px 16px #0003;
    }
    @keyframes fadeSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: none; } }
    .fade-slide-up { animation: fadeSlideUp 0.9s cubic-bezier(.39,.575,.565,1) both; }
</style>
<div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center" style="padding-top: 40px; padding-bottom: 40px;">
    <div class="text-center mb-3" style="width:100%;">
        <img src="{{ asset('assets/img/logo_limit_x.png') }}" alt="Limit X Logo" style="height:60px; max-width:180px; object-fit:contain; display:block; margin-left:auto; margin-right:auto;">
    </div>
    <div class="reset-card d-flex flex-column align-items-center justify-content-center fade-slide-up">
        <h3 class="mb-3 fw-bold text-center" style="color:#fff;">Reset Password</h3>
        @if (session('status'))
            <div class="alert alert-success mb-3">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('password.store') }}" class="w-100 d-flex flex-column align-items-center">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div class="w-100" style="max-width: 270px;">
                <label for="email" class="reset-label">Email</label>
                <input id="email" type="email" name="email" class="form-control reset-input @error('email') is-invalid @enderror" placeholder="Masukkan Email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-100" style="max-width: 270px;">
                <label for="password" class="reset-label">Password Baru</label>
                <input id="password" type="password" name="password" class="form-control reset-input @error('password') is-invalid @enderror" placeholder="Password Baru" required autocomplete="new-password">
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-100" style="max-width: 270px;">
                <label for="password_confirmation" class="reset-label">Konfirmasi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control reset-input @error('password_confirmation') is-invalid @enderror" placeholder="Konfirmasi Password Baru" required autocomplete="new-password">
                @error('password_confirmation')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn reset-btn">Reset Password</button>
        </form>
    </div>
</div>
@section('scripts')
<script>
function togglePassword(id, el) {
    const input = document.getElementById(id);
    const icon = document.getElementById('toggleIcon-' + id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    }
}
</script>
@endsection
@endsection
