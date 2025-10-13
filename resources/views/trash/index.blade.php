@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 animate-fade-in-up">
    <div class="flex justify-between items-center mb-4 animate-fade-in-up">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                <i class="bi bi-trash"></i> Sampah
            </h2>
            <p class="text-gray-600 mt-1">
                <i class="bi bi-info-circle"></i> 
                Form yang dihapus akan tersimpan di sini selama 30 hari. Anda dapat mengembalikan atau menghapus permanen.
            </p>
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
                        <i class="bi bi-funnel"></i> Tipe:
                    </label>
                    <select name="type" class="form-select">
                        <option value="all" {{ $type == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="kimia" {{ $type == 'kimia' ? 'selected' : '' }}>Kimia</option>
                        <option value="mikrobiologi" {{ $type == 'mikrobiologi' ? 'selected' : '' }}>Mikrobiologi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1 fw-semibold text-primary">
                        <i class="bi bi-list"></i> Tampil:
                    </label>
                    <select name="perPage" class="form-select">
                        <option value="10" {{ $perPage==10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $perPage==20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $perPage==50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage==100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-funnel"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('trash.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($type === 'all' || $type === 'kimia')
    <!-- Kimia Forms -->
    <div class="mb-6 animate-fade-in-up">
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <div class="bg-blue-100 px-4 py-3 border-b">
                <h3 class="text-lg font-semibold text-blue-900">
                    <i class="bi bi-flask"></i> Form Kimia ({{ $kimiaForms->total() }})
                </h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-200">
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Judul</th>
                        <th class="px-4 py-2">No Form</th>
                        <th class="px-4 py-2">Tanggal</th>
                        <th class="px-4 py-2">Dihapus</th>
                        <th class="px-4 py-2">Berapa Hari</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kimiaForms as $form)
                    <tr class="hover:bg-yellow-50">
                        <td class="px-4 py-2">{{ $loop->iteration + ($kimiaForms->currentPage()-1)*$kimiaForms->perPage() }}</td>
                        <td class="px-4 py-2">{{ $form->title }}</td>
                        <td class="px-4 py-2">{{ $form->no }}</td>
                        <td class="px-4 py-2">{{ $form->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $form->deleted_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="badge bg-warning text-dark">
                                {{ $form->deleted_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-4 py-2 flex gap-2">
                            <form action="{{ route('trash.restore-kimia', $form->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Kembalikan form ini?')">
                                    <i class="bi bi-arrow-counterclockwise"></i> Kembalikan
                                </button>
                            </form>
                            <form action="{{ route('trash.force-delete-kimia', $form->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tindakan ini tidak dapat dibatalkan!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash-fill"></i> Hapus Permanen
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8">
                            <div class="text-gray-500">
                                <i class="bi bi-inbox text-4xl mb-2"></i>
                                <p class="text-lg font-medium">Tidak ada form kimia di sampah</p>
                                <p class="text-sm">Form kimia yang dihapus akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($kimiaForms->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $kimiaForms->links() }}
        </div>
        @endif
    </div>
    @endif

    @if($type === 'all' || $type === 'mikrobiologi')
    <!-- Mikrobiologi Forms -->
    <div class="mb-6 animate-fade-in-up">
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <div class="bg-green-100 px-4 py-3 border-b">
                <h3 class="text-lg font-semibold text-green-900">
                    <i class="bi bi-bug"></i> Form Mikrobiologi ({{ $mikrobiologiForms->total() }})
                </h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-green-200">
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Judul</th>
                        <th class="px-4 py-2">No Form</th>
                        <th class="px-4 py-2">Tgl Inokulasi</th>
                        <th class="px-4 py-2">Dihapus</th>
                        <th class="px-4 py-2">Berapa Hari</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mikrobiologiForms as $form)
                    <tr class="hover:bg-yellow-50">
                        <td class="px-4 py-2">{{ $loop->iteration + ($mikrobiologiForms->currentPage()-1)*$mikrobiologiForms->perPage() }}</td>
                        <td class="px-4 py-2">{{ $form->title }}</td>
                        <td class="px-4 py-2">{{ $form->no }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($form->tgl_inokulasi)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $form->deleted_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="badge bg-warning text-dark">
                                {{ $form->deleted_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-4 py-2 flex gap-2">
                            <form action="{{ route('trash.restore-mikrobiologi', $form->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Kembalikan form ini?')">
                                    <i class="bi bi-arrow-counterclockwise"></i> Kembalikan
                                </button>
                            </form>
                            <form action="{{ route('trash.force-delete-mikrobiologi', $form->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus permanen? Tindakan ini tidak dapat dibatalkan!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash-fill"></i> Hapus Permanen
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8">
                            <div class="text-gray-500">
                                <i class="bi bi-inbox text-4xl mb-2"></i>
                                <p class="text-lg font-medium">Tidak ada form mikrobiologi di sampah</p>
                                <p class="text-sm">Form mikrobiologi yang dihapus akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($mikrobiologiForms->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $mikrobiologiForms->links() }}
        </div>
        @endif
    </div>
    @endif
</div>

<style>
@keyframes fade-in-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }
.animate-fade-in-up { animation: fade-in-up 0.6s ease-out both; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Alert untuk hapus permanen
    @if(session('success') && str_contains(session('success'), 'permanen'))
        alert('{{ session("success") }}');
    @endif
});
</script>
@endsection
