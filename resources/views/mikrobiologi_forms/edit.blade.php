@extends('layouts.app')

@section('content')
<style>
@keyframes fadeSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: none; } }
.fade-slide-up { animation: fadeSlideUp 0.9s cubic-bezier(.39,.575,.565,1) both; }
</style>
<div class="max-w-xl mx-auto py-6 fade-slide-up">
    <h2 class="text-2xl font-bold text-green-900 mb-4">Edit Form Mikrobiologi</h2>
    <form action="{{ route('mikrobiologi-forms.update', $form) }}?from=show" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4 fade-slide-up">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-green-900 font-semibold">Judul</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2" required value="{{ old('title', $form->title) }}">
            @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">No Form</label>
            <input type="text" name="no" class="w-full border rounded px-3 py-2" required value="{{ old('no', $form->no) }}">
            @error('no')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">Tanggal Inokulasi</label>
            <input type="date" name="tgl_inokulasi" class="w-full border rounded px-3 py-2" required value="{{ old('tgl_inokulasi', $form->tgl_inokulasi) }}">
            @error('tgl_inokulasi')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-green-900 font-semibold">Tanggal Pengamatan</label>
            <div id="obs-wrapper" class="space-y-2">
                @php $obs = $form->observations()->orderBy('tanggal')->get(); @endphp
                @if(old('observations'))
                    @foreach(old('observations') as $i => $o)
                    <div class="flex gap-2 items-start">
                        <input type="hidden" name="observations[{{ $i }}][id]" value="{{ $o['id'] ?? '' }}">
                        <input type="date" name="observations[{{ $i }}][tanggal]" class="border rounded px-3 py-2" value="{{ $o['tanggal'] ?? '' }}">
                        <input type="text" name="observations[{{ $i }}][keterangan]" class="flex-1 border rounded px-3 py-2" placeholder="Keterangan (opsional)" value="{{ $o['keterangan'] ?? '' }}">
                        <button type="button" class="bg-red-500 text-white px-3 py-2 rounded remove-obs">Hapus</button>
                    </div>
                    @endforeach
                @else
                    @forelse($obs as $i => $o)
                    <div class="flex gap-2 items-start">
                        <input type="hidden" name="observations[{{ $i }}][id]" value="{{ $o->id }}">
                        <input type="date" name="observations[{{ $i }}][tanggal]" class="border rounded px-3 py-2" value="{{ $o->tanggal }}">
                        <input type="text" name="observations[{{ $i }}][keterangan]" class="flex-1 border rounded px-3 py-2" placeholder="Keterangan (opsional)" value="{{ $o->keterangan }}">
                        <button type="button" class="bg-red-500 text-white px-3 py-2 rounded remove-obs">Hapus</button>
                    </div>
                    @empty
                    <div class="flex gap-2 items-start">
                        <input type="date" name="observations[0][tanggal]" class="border rounded px-3 py-2" value="{{ old('tgl_pengamatan', $form->tgl_pengamatan) }}">
                        <input type="text" name="observations[0][keterangan]" class="flex-1 border rounded px-3 py-2" placeholder="Keterangan (opsional)">
                        <button type="button" class="bg-red-500 text-white px-3 py-2 rounded remove-obs">Hapus</button>
                    </div>
                    @endforelse
                @endif
            </div>
            <div class="mt-2 flex gap-2">
                <button type="button" id="add-obs" class="bg-blue-600 text-white px-3 py-2 rounded">+ Tambah Tanggal</button>
            </div>
            <input type="hidden" name="tgl_pengamatan" value="{{ old('tgl_pengamatan', $form->tgl_pengamatan) }}">
            @error('observations.*.tanggal')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('mikrobiologi-forms.index') }}" class="bg-gray-300 text-green-900 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" class="bg-yellow-400 text-green-900 px-4 py-2 rounded hover:bg-yellow-500">Update</button>
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
      const idInput = row.querySelector('input[type="hidden"]');
      if (idInput) idInput.name = `observations[${idx}][id]`;
      row.querySelectorAll('input').forEach(inp=>{
        if (inp.type==='date') inp.name = `observations[${idx}][tanggal]`;
        if (inp.type==='text') inp.name = `observations[${idx}][keterangan]`;
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