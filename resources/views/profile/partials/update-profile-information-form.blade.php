<div class="card shadow border-0 h-100" style="border-radius: 1.3rem; background: linear-gradient(120deg, #f1f5f9 0%, #f8fafc 100%);">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                <i class="bi bi-person-gear text-success" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <h4 class="fw-bold text-success mb-1">Edit Profil</h4>
                <p class="text-muted mb-0 small">Kelola informasi pribadi Anda</p>
            </div>
        </div>

        <form method="post" action="{{ route('profile.update') }}" class="mb-0">
            @csrf
            @method('patch')
            
            <div class="mb-4">
                <label for="name" class="form-label fw-semibold text-dark">
                    <i class="bi bi-person me-2 text-success"></i>Nama Lengkap
                </label>
                <input id="name" name="name" type="text" 
                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                       value="{{ old('name', $user->name) }}" 
                       required autofocus autocomplete="name"
                       style="border-radius: 1rem; border: 2px solid #e2e8f0; padding: 0.75rem 1rem;"
                       placeholder="Masukkan nama lengkap Anda">
                @error('name')
                    <div class="invalid-feedback d-block mt-2">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="form-label fw-semibold text-dark">
                    <i class="bi bi-envelope me-2 text-success"></i>Alamat Email
                </label>
                <input id="email" name="email" type="email" 
                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                       value="{{ old('email', $user->email) }}" 
                       required autocomplete="username"
                       style="border-radius: 1rem; border: 2px solid #e2e8f0; padding: 0.75rem 1rem;"
                       placeholder="Masukkan alamat email Anda">
                @error('email')
                    <div class="invalid-feedback d-block mt-2">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
                
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="alert alert-warning py-3 px-4 mt-3" style="border-radius: 1rem; border: none; background: linear-gradient(120deg, #fef3c7 0%, #fde68a 100%);">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                            <div class="flex-grow-1">
                                <strong>Email belum diverifikasi!</strong><br>
                                <small>Email Anda belum diverifikasi. 
                                <button form="send-verification" class="btn btn-link p-0 align-baseline text-decoration-underline fw-semibold">
                                    Kirim ulang verifikasi
                                </button></small>
                            </div>
                        </div>
                    </div>
                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success py-3 px-4 mt-3" style="border-radius: 1rem; border: none; background: linear-gradient(120deg, #d1fae5 0%, #a7f3d0 100%);">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Berhasil!</strong> Link verifikasi baru sudah dikirim ke email Anda.
                        </div>
                    @endif
                @endif
            </div>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success py-3 px-4 mb-4" style="border-radius: 1rem; border: none; background: linear-gradient(120deg, #d1fae5 0%, #a7f3d0 100%);">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Berhasil!</strong> Profil berhasil diupdate.
                </div>
            @endif

            <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg fw-bold px-4 py-3" 
                        style="border-radius: 1.2rem; box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);">
                    <i class="bi bi-check-circle me-2"></i>Simpan Profil
                </button>
            </div>
        </form>
    </div>
</div>
