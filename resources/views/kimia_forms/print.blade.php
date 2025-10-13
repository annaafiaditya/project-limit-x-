@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <h2 class="h4 mb-0">{{ $kimia_form->title }} — {{ $kimia_form->no }}</h2>
        <button class="btn btn-sm btn-primary" onclick="window.print()">Cetak / PDF</button>
    </div>

    <div class="mb-3">
        <div><strong>No Form:</strong> {{ $kimia_form->no }}</div>
        <div><strong>Tanggal:</strong> {{ $kimia_form->tanggal }}</div>
    </div>

    @foreach($tables as $table)
    <div class="mb-4">
        <h5 class="fw-bold">{{ $table->name }}</h5>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    @foreach($table->columns as $col)
                        <th>{{ $col->nama_kolom }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($table->entries as $entry)
                    <tr>
                        @foreach($table->columns as $col)
                            <td>
                                @if(isset($entry->data[$col->id]))
                                    @if($col->tipe_kolom === 'date')
                                        {{ \Carbon\Carbon::parse($entry->data[$col->id])->format('d/m/Y') }}
                                    @elseif($col->tipe_kolom === 'decimal')
                                        {{ number_format($entry->data[$col->id], 2) }}
                                    @else
                                        {{ $entry->data[$col->id] }}
                                    @endif
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ $table->columns->count() }}" class="text-center">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endforeach

    <div class="mt-5">
        <h6 class="fw-bold">Approval / Signature</h6>
        <table class="table table-bordered table-sm w-auto">
            <thead><tr><th>Role</th><th>Nama</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
                @foreach($signatures as $sig)
                    <tr>
                        <td>{{ $sig->role }}</td>
                        <td>{{ $sig->name }}</td>
                        <td>{{ ucfirst($sig->status) }}</td>
                        <td>{{ $sig->tanggal }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    .navbar, .btn, .alert { display: none !important; }
    table { page-break-inside: avoid; }
}
</style>
@endsection
