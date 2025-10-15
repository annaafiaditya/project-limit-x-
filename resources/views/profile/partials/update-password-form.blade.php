<div class="card shadow border-0 h-100" style="border-radius: 1.3rem; background: linear-gradient(120deg, #f1f5f9 0%, #f8fafc 100%);">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                <i class="bi bi-shield-lock text-primary" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <h4 class="fw-bold text-primary mb-1">Ubah Password</h4>
                <p class="text-muted mb-0 small">Perbarui kata sandi untuk keamanan</p>
            </div>
        </div>

    <form method="post" action="{{ route('password.update') }}" class="mb-0">
        @csrf
        @method('put')
            
            <div class="mb-4">
                <label for="update_password_current_password" class="form-label fw-semibold text-dark">
                    <i class="bi bi-key me-2 text-primary"></i>Password Lama
                </label>
                <input id="update_password_current_password" name="current_password" type="password" 
                        class="form-control form-control-lg @error('current_password', 'updatePassword') is-invalid @enderror" 
                        autocomplete="current-password"
                        style="border-radius: 1rem; border: 2px solid #e2e8f0; padding: 0.75rem 1rem;"
                        placeholder="Masukkan password lama Anda">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback d-block mt-2">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="update_password_password" class="form-label fw-semibold text-dark">
                    <i class="bi bi-lock me-2 text-primary"></i>Password Baru
                </label>
                <input id="update_password_password" name="password" type="password" 
                        class="form-control form-control-lg @error('password', 'updatePassword') is-invalid @enderror" 
                        autocomplete="new-password"
                        style="border-radius: 1rem; border: 2px solid #e2e8f0; padding: 0.75rem 1rem;"
                        placeholder="Masukkan password baru">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback d-block mt-2">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="update_password_password_confirmation" class="form-label fw-semibold text-dark">
                    <i class="bi bi-check-circle me-2 text-primary"></i>Konfirmasi Password Baru
                </label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                        class="form-control form-control-lg @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                        autocomplete="new-password"
                        style="border-radius: 1rem; border: 2px solid #e2e8f0; padding: 0.75rem 1rem;"
                        placeholder="Konfirmasi password baru">
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback d-block mt-2">
                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

        @if (session('status') === 'password-updated')
                <div class="alert alert-success py-3 px-4 mb-4" style="border-radius: 1rem; border: none; background: linear-gradient(120deg, #d1fae5 0%, #a7f3d0 100%);">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Berhasil!</strong> Password berhasil diubah.
                </div>
        @endif

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg fw-bold px-4 py-3" 
                        style="border-radius: 1.2rem; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);">
                    <i class="bi bi-shield-check me-2"></i>Simpan Password
                </button>
            </div>
    </form>
    </div>
</div>

