<div class="card shadow border-0" style="border-radius: 1.3rem; background: linear-gradient(120deg, #fef2f2 0%, #fee2e2 100%); border: 2px solid #fecaca;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3">
                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <h4 class="fw-bold text-danger mb-1">Hapus Akun</h4>
                <p class="text-muted mb-0 small">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>

        <div class="alert alert-danger py-3 px-4 mb-4" style="border-radius: 1rem; border: none; background: linear-gradient(120deg, #fef2f2 0%, #fee2e2 100%);">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill text-danger me-3 mt-1"></i>
                <div>
                    <strong class="text-danger">Peringatan!</strong><br>
                    <small class="text-muted">
                        Setelah akun Anda dihapus, semua data dan sumber daya akan dihapus secara permanen. 
                        Sebelum menghapus akun, silakan unduh data atau informasi yang ingin Anda simpan.
                    </small>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-danger btn-lg fw-bold px-4 py-3" 
                data-bs-toggle="modal" data-bs-target="#confirmUserDeletion"
                style="border-radius: 1.2rem; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);">
            <i class="bi bi-trash me-2"></i>Hapus Akun
        </button>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="confirmUserDeletion" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 1.5rem; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0" style="background: linear-gradient(120deg, #fef2f2 0%, #fee2e2 100%); border-radius: 1.5rem 1.5rem 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-20 p-2 rounded-circle me-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-danger" id="confirmUserDeletionLabel">
                        Konfirmasi Penghapusan Akun
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

                <div class="modal-body p-4">
                    <div class="alert alert-warning py-3 px-4 mb-4" style="border-radius: 1rem; border: none; background: linear-gradient(120deg, #fef3c7 0%, #fde68a 100%);">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-3 mt-1"></i>
                            <div>
                                <strong class="text-warning">Apakah Anda yakin ingin menghapus akun?</strong><br>
                                <small class="text-muted">
                                    Setelah akun Anda dihapus, semua data dan sumber daya akan dihapus secara permanen. 
                                    Masukkan password Anda untuk mengkonfirmasi penghapusan akun secara permanen.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold text-dark">
                            <i class="bi bi-key me-2 text-danger"></i>Password Konfirmasi
                        </label>
                        <input id="password" name="password" type="password" 
                               class="form-control form-control-lg @error('password', 'userDeletion') is-invalid @enderror" 
                               placeholder="Masukkan password untuk konfirmasi"
                               style="border-radius: 1rem; border: 2px solid #e2e8f0; padding: 0.75rem 1rem;">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback d-block mt-2">
                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
            </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-lg px-4 py-2" 
                            data-bs-dismiss="modal" style="border-radius: 1rem;">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger btn-lg px-4 py-2" 
                            style="border-radius: 1rem; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);">
                        <i class="bi bi-trash me-2"></i>Hapus Akun
                    </button>
            </div>
        </form>
        </div>
    </div>
</div>
