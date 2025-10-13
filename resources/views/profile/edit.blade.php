@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <!-- Hero Section -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between p-4 p-md-5 shadow-lg hero-profile-card" 
                style="border-radius: 2rem; background: linear-gradient(120deg, #e0f7fa 0%, #f8fafc 100%); border: 1.5px solid #e0e7ef; box-shadow: 0 8px 32px #0001;">
                
                <!-- Teks Sambutan -->
                <div class="mb-4 mb-md-0 text-center text-md-start flex-grow-1">
                    <h1 class="fw-bold text-success mb-3" style="font-size: 2.6rem; letter-spacing: 0.5px; text-shadow: 0 2px 6px #b6f0e6;">
                        <i class="bi bi-person-circle me-3"></i>Profile Settings
                    </h1>

                    <div style="height: 4px; width: 90px; background: linear-gradient(90deg, #34d399, #60a5fa, #fbbf24); border-radius: 2px; margin-bottom: 1.2rem;"></div>

                    <p class="lead text-secondary mb-3" style="font-size: 1.15rem; line-height: 1.7;">
                        Kelola informasi profil dan keamanan akun Anda.<br>
                        Pastikan data Anda selalu <strong>terbaru</strong> dan <strong>aman</strong>.
                    </p>
                </div>
            </div>
        </div>

        <!-- Profile Forms -->
        <div class="col-12 col-lg-10 mb-4 fade-slide-up fade-slide-up-delay-1">
            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    @include('profile.partials.update-profile-information-form')
                </div>
                <div class="col-12 col-lg-6">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

    </div>
</div>

<style>
@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: none; }
}
.fade-slide-up { animation: fadeSlideUp 0.9s cubic-bezier(.39,.575,.565,1) both; }
.fade-slide-up-delay-1 { animation-delay: .15s; }
.fade-slide-up-delay-2 { animation-delay: .3s; }
.hero-profile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px #0001;
    transition: all 0.3s ease;
}
</style>
@endsection
