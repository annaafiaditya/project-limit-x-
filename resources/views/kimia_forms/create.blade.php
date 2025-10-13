@extends('layouts.app')

@section('content')
<style>
@keyframes fadeSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: none; } }
.fade-slide-up { animation: fadeSlideUp 0.9s cubic-bezier(.39,.575,.565,1) both; }
</style>
<div class="max-w-xl mx-auto py-6 fade-slide-up">
    <h2 class="text-2xl font-bold text-blue-900 mb-4">Buat Form Kimia</h2>
    @if(isset($template) && $template)
        <div class="alert alert-info mb-4">Membuat form dari template: <b>{{ $template->title }}</b></div>
    @endif
    <form action="{{ route('kimia.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4 fade-slide-up">
        @csrf
        <div>
            <label class="block text-blue-900 font-semibold">Judul</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2" required value="{{ old('title', isset($template) && $template ? $template->title : '') }}">
            @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-blue-900 font-semibold">No Form</label>
            <input type="text" name="no" class="w-full border rounded px-3 py-2" required value="{{ old('no', $suggested_no ?? '') }}">
            @if(isset($template) && $template)
                <div class="text-sm text-blue-600 mt-1">
                    <i class="bi bi-info-circle"></i> No Form otomatis dengan format: <strong>01/LAMK/V/25</strong> (nomor/jenis/bulan romawi/tahun). Nomor akan otomatis increment untuk judul yang sama. Anda bisa edit jenis langsung di field ini.
                </div>
            @endif
            @error('no')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-blue-900 font-semibold">Tanggal</label>
            <input type="date" name="tanggal" class="w-full border rounded px-3 py-2" required value="{{ old('tanggal') }}">
            @error('tanggal')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        @if(isset($tables) && $tables->count())
        <div class="mb-4">
            <label class="block text-blue-900 font-semibold mb-2">Tabel dari Template</label>
            <div class="space-y-3">
                @foreach($tables as $table)
                    <div class="border rounded p-3 bg-gray-50">
                        <h4 class="font-semibold text-blue-800 mb-2">{{ $table->name }}</h4>
                        <ul class="list-disc pl-6 text-sm">
                            @foreach($table->columns as $col)
                                <li>{{ $col->nama_kolom }} ({{ $col->tipe_kolom }})</li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
            <div class="alert alert-info mt-3">
                <strong>Info:</strong> Semua tabel dan kolom di atas akan otomatis disalin ke form baru. Data entry tidak akan disalin.
            </div>
            <input type="hidden" name="template_title" value="{{ $template->title }}">
        </div>
        @endif
        <div class="flex justify-end gap-2">
            <a href="{{ route('kimia.index') }}" class="bg-gray-300 text-blue-900 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
