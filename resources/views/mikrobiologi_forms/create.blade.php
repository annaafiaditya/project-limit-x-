@extends('layouts.app')

@section('content')
<style>
@keyframes fadeSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: none; } }
.fade-slide-up { animation: fadeSlideUp 0.9s cubic-bezier(.39,.575,.565,1) both; }
</style>
<div class="max-w-xl mx-auto py-6 fade-slide-up">
    <h2 class="text-2xl font-bold text-green-900 mb-4">Buat Form Mikrobiologi</h2>
    @if(isset($template) && $template)
        <div class="alert alert-info mb-4">Membuat form dari template: <b>{{ $template->title }}</b></div>
    @endif
    <form action="{{ route('mikrobiologi-forms.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4 fade-slide-up">
        @csrf
        <div>
            <label class="block text-green-900 font-semibold">Judul</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2" required value="{{ old('title', isset($template) && $template ? $template->title : '') }}">
            @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">No Form</label>
            <input type="text" name="no" class="w-full border rounded px-3 py-2" required value="{{ old('no', $suggested_no ?? '') }}">
            @if(isset($template) && $template)
                <div class="text-sm text-green-600 mt-1">
                    <i class="bi bi-info-circle"></i> No Form otomatis dengan format: <strong>01/LAMK/V/25</strong> (nomor/jenis/bulan romawi/tahun). Nomor akan otomatis increment untuk judul yang sama. Anda bisa edit jenis langsung di field ini.
                </div>
            @endif
            @error('no')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">Tanggal Inokulasi</label>
            <input type="date" name="tgl_inokulasi" class="w-full border rounded px-3 py-2" required value="{{ old('tgl_inokulasi') }}">
            @error('tgl_inokulasi')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">Tanggal Pengamatan</label>
            <div id="obs-wrapper" class="space-y-2">
                @php $oldObs = old('observations', [['tanggal'=>'','keterangan'=>'']]); @endphp
                @foreach($oldObs as $i => $obs)
                <div class="flex gap-2 items-start">
                    <input type="date" name="observations[{{ $i }}][tanggal]" class="border rounded px-3 py-2" value="{{ $obs['tanggal'] ?? '' }}">
                    <input type="text" name="observations[{{ $i }}][keterangan]" class="flex-1 border rounded px-3 py-2" placeholder="Keterangan (opsional)" value="{{ $obs['keterangan'] ?? '' }}">
                    <button type="button" class="bg-red-500 text-white px-3 py-2 rounded remove-obs">Hapus</button>
                </div>
                @endforeach
            </div>
            <div class="mt-2 flex gap-2">
                <button type="button" id="add-obs" class="bg-blue-600 text-white px-3 py-2 rounded">+ Tambah Tanggal</button>
            </div>
            <input type="hidden" name="tgl_pengamatan" value="{{ old('tgl_pengamatan') }}">
            @error('observations.*.tanggal')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        @if(isset($columns) && $columns->count())
        <div class="mb-4">
            <label class="block text-green-900 font-semibold mb-2">Kolom Data Entry (dari template)</label>
            <ul class="list-disc pl-6 mb-2">
                @foreach($columns as $col)
                    <li>{{ $col->nama_kolom }} ({{ $col->tipe_kolom }})</li>
                    <input type="hidden" name="columns[nama_kolom][]" value="{{ $col->nama_kolom }}">
                    <input type="hidden" name="columns[tipe_kolom][]" value="{{ $col->tipe_kolom }}">
                    <input type="hidden" name="columns[urutan][]" value="{{ $col->urutan }}">
                @endforeach
            </ul>
            <div class="alert alert-warning">Kolom di atas otomatis diambil dari template, akan langsung tersedia setelah form disimpan.</div>
            <input type="hidden" name="template_title" value="{{ $template->title }}">
        </div>
        @endif
        <div class="flex justify-end gap-2">
            <a href="{{ route('mikrobiologi-forms.index') }}" class="bg-gray-300 text-green-900 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
        </div>
    </form>
</div>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const wrapper = document.getElementById('obs-wrapper');
  const addBtn = document.getElementById('add-obs');
  function indexRows(){
    [...wrapper.querySelectorAll('div.flex')].forEach((row, idx)=>{
      row.querySelectorAll('input').forEach(inp=>{
        if (inp.name.includes('tanggal')) inp.name = `observations[${idx}][tanggal]`;
        if (inp.name.includes('keterangan')) inp.name = `observations[${idx}][keterangan]`;
      });
    });
  }
  addBtn?.addEventListener('click',()=>{
    const div = document.createElement('div');
    div.className = 'flex gap-2 items-start';
    div.innerHTML = `<input type="date" name="observations[][tanggal]" class="border rounded px-3 py-2">
    <input type="text" name="observations[][keterangan]" class="flex-1 border rounded px-3 py-2" placeholder="Keterangan (opsional)">
    <button type="button" class="bg-red-500 text-white px-3 py-2 rounded remove-obs">Hapus</button>`;
    wrapper.appendChild(div);
    indexRows();
  });
  wrapper?.addEventListener('click', (e)=>{
    const btn = e.target.closest('.remove-obs');
    if (!btn) return;
    const row = btn.closest('div.flex');
    row.remove();
    indexRows();
  });
});
</script>
@endpush