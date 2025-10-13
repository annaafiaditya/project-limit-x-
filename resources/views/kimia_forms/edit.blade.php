@extends('layouts.app')

@section('content')
<style>
@keyframes fadeSlideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: none; } }
.fade-slide-up { animation: fadeSlideUp 0.9s cubic-bezier(.39,.575,.565,1) both; }
</style>
<div class="max-w-xl mx-auto py-6 fade-slide-up">
    <h2 class="text-2xl font-bold text-blue-900 mb-4">Edit Form Kimia</h2>
    <form action="{{ route('kimia.update', $form) }}?from=show" method="POST" class="bg-white shadow rounded-lg p-6 space-y-4 fade-slide-up">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-blue-900 font-semibold">Judul</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2" required value="{{ old('title', $form->title) }}">
            @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-blue-900 font-semibold">No Form</label>
            <input type="text" name="no" class="w-full border rounded px-3 py-2" required value="{{ old('no', $form->no) }}">
            @error('no')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-blue-900 font-semibold">Tanggal</label>
            <input type="date" name="tanggal" class="w-full border rounded px-3 py-2" required value="{{ old('tanggal', $form->tanggal->format('Y-m-d')) }}">
            @error('tanggal')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('kimia.show', $form) }}" class="bg-gray-300 text-blue-900 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
        </div>
    </form>
</div>
@endsection
