@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 animate-fade-in-up">
    <div class="flex justify-between items-center mb-4 animate-fade-in-up">
        <h2 class="text-2xl font-bold text-blue-900">Data Form Kimia</h2>
        <div class="flex gap-2 align-items-center">
            @if(Auth::user()->canPerformActions())
                <a href="{{ route('kimia.create') }}" class="btn btn-primary px-4 py-2">+ Tambah Form</a>
                @if($template_titles->count())
                <form method="GET" action="{{ route('kimia.create') }}" class="d-flex align-items-center gap-2 bg-white border border-success rounded px-2 py-1 shadow-sm" style="max-width: 340px;">
                    <select name="template_title" id="template_title" class="form-select form-select-sm" style="min-width:220px; max-width:320px;" required>
                        <option value="" disabled selected>Pilih template...</option>
                        @foreach($template_titles as $title)
                            <option value="{{ $title }}">{{ $title }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                        <i class="bi bi-files"></i> Duplikat
                    </button>
                </form>
                @endif
            @endif
        </div>
    </div>

    <!-- Filter Approval + Export All -->
    <div class="mb-4 animate-fade-in-up">
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <label class="form-label mb-0 fw-semibold text-primary">
                            <i class="bi bi-funnel"></i> Filter Approval:
                        </label>
                        <form method="GET" action="{{ route('kimia.index') }}" class="d-flex align-items-center gap-2">
                            <select name="approval" class="form-select" style="min-width: 220px;" onchange="this.form.submit()">
                                <option value="">📋 Semua Approval</option>
                                <option value="pending" {{ request('approval') == 'pending' ? 'selected' : '' }}>⏳ Belum Lengkap Semua</option>
                                <option value="completed" {{ request('approval') == 'completed' ? 'selected' : '' }}>✅ Sudah Lengkap Semua</option>
                                <option value="technician" {{ request('approval') == 'technician' ? 'selected' : '' }}>🔬 QA Lab. Technician</option>
                                <option value="staff" {{ request('approval') == 'staff' ? 'selected' : '' }}>👥 QA Staff</option>
                                <option value="supervisor" {{ request('approval') == 'supervisor' ? 'selected' : '' }}>👨‍💼 QA Supervisor</option>
                            </select>
                            @foreach(request()->except('approval', 'page') as $key => $val)
                                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                            @endforeach
                        </form>
                        @if(request('approval'))
                            <a href="{{ route('kimia.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x-circle"></i> Reset Filter
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <form method="GET" action="{{ route('kimia.export-all') }}" class="d-inline">
                        @foreach(request()->except('page') as $key => $val)
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endforeach
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-file-earmark-excel"></i> Export Excel All
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Search -->
    <div class="mb-4 animate-fade-in-up">
        <div class="bg-white rounded-lg shadow-sm p-4 border">
            <form method="GET" action="" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1 fw-semibold text-primary">
                        <i class="bi bi-search"></i> Cari:
                    </label>
                    <input type="text" name="search" placeholder="Judul, No Form, atau Tanggal..." value="{{ $search }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1 fw-semibold text-primary">
                        <i class="bi bi-calendar"></i> Tanggal:
                    </label>
                    <input type="date" name="search_tgl" value="{{ $search_tgl }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1 fw-semibold text-primary">
                        <i class="bi bi-collection"></i> Judul:
                    </label>
                    <select name="group_title" class="form-select">
                        <option value="">-- Semua Judul --</option>
                        @foreach($titles as $title)
                            <option value="{{ $title }}" {{ $group_title == $title ? 'selected' : '' }}>{{ $title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-funnel"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('kimia.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($group_title)
        <div class="alert alert-info py-2 mb-3 animate-fade-in-up">Menampilkan data untuk judul: <b>{{ $group_title }}</b></div>
    @endif

    
    <div class="bg-white shadow rounded-lg overflow-x-auto animate-fade-in-up" style="animation-delay:.15s; animation-duration:.8s;">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-blue-200">
                <tr>
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Judul</th>
                    <th class="px-4 py-2">No Form</th>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forms as $form)
                <tr class="hover:bg-yellow-50 cursor-pointer" onclick="window.location='{{ route('kimia.show', $form) }}'" data-form-id="{{ $form->id }}" data-entry-count="{{ $form->entries->count() }}" data-approval-count="{{ $form->signatures->where('status', 'accept')->count() }}">
                    <td class="px-4 py-2">{{ $loop->iteration + ($forms->currentPage()-1)*$forms->perPage() }}</td>
                    <td class="px-4 py-2">{{ $form->title }}</td>
                    <td class="px-4 py-2">{{ $form->no }}</td>
                    <td class="px-4 py-2">{{ $form->tanggal->format('d/m/Y') }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('kimia.show', $form) }}" class="text-blue-600 hover:underline">Lihat</a>
                        @if(Auth::user()->canPerformActions())
                            <a href="{{ route('kimia.edit', $form) }}" class="text-yellow-600 hover:underline">Edit</a>
                        @endif
                        <a href="{{ route('kimia.export', $form) }}" class="text-green-600 hover:underline">Export Excel</a>
                        @if(Auth::user()->canPerformActions())
                            <form action="{{ route('kimia.destroy', $form) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 animate-fade-in-up">
        <div></div>
        <div class="d-flex align-items-center gap-2 p-2 bg-white rounded shadow-sm border" style="min-width:260px;">
            <form method="GET" action="" id="perPageForm" class="d-flex align-items-center me-2">
                @foreach(request()->except('perPage', 'page') as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach
                <label for="perPage" class="me-2 mb-0 fw-semibold text-primary" style="font-size:0.95em;">Tampil:</label>
                <select name="perPage" id="perPage" class="form-select form-select-sm w-auto border-primary text-primary fw-bold" style="background-color:#e9f2fe; font-size:0.95em;" onchange="document.getElementById('perPageForm').submit()">
                    <option value="10" {{ $perPage==10 ? 'selected' : '' }}>10</option>
                    <option value="20" {{ $perPage==20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ $perPage==50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage==100 ? 'selected' : '' }}>100</option>
                </select>
            </form>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    @if ($forms->onFirstPage())
                        <li class="page-item disabled"><span class="page-link bg-light border-0">&laquo;</span></li>
                    @else
                        <li class="page-item"><a class="page-link bg-warning text-primary border-0 fw-bold" href="{{ $forms->previousPageUrl() }}">&laquo;</a></li>
                    @endif
                    @foreach ($forms->getUrlRange(1, $forms->lastPage()) as $page => $url)
                        <li class="page-item {{ $page == $forms->currentPage() ? 'active' : '' }}">
                            <a class="page-link {{ $page == $forms->currentPage() ? 'bg-primary text-white border-primary' : 'bg-light text-primary border-0' }} fw-bold" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                    @if ($forms->hasMorePages())
                        <li class="page-item"><a class="page-link bg-warning text-primary border-0 fw-bold" href="{{ $forms->nextPageUrl() }}">&raquo;</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link bg-light border-0">&raquo;</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

<style>
@keyframes fade-in-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }
.animate-fade-in-up { animation: fade-in-up 0.6s ease-out both; }
@keyframes highlight { 
    0% { background-color: #fef3c7; } 
    100% { background-color: transparent; } 
}
.highlight-restored { 
    animation: highlight 5.5s ease-out; 
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('highlighted_form'))
        const formId = {{ session('highlighted_form') }};
        const formRow = document.querySelector(`tr[data-form-id="${formId}"]`);
        if (formRow) {
            formRow.classList.add('highlight-restored');
            formRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    @endif
    
    // Alert untuk form yang dihapus
    @if(session('info'))
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-info alert-dismissible fade show';
        alertDiv.style.backgroundColor = '#f8d7da';
        alertDiv.style.borderColor = '#f5c6cb';
        alertDiv.style.color = '#721c24';
        alertDiv.innerHTML = `
            <i class="bi bi-info-circle"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.querySelector('.max-w-7xl').insertBefore(alertDiv, document.querySelector('.max-w-7xl').firstChild);
        
        // Auto hide setelah 5 detik
        setTimeout(function() {
            if(alertDiv) {
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 300);
            }
        }, 5000);
    @endif
});
</script>
@endsection
