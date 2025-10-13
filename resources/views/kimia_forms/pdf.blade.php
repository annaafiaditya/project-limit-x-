<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $kimia_form->title }} - {{ $kimia_form->no }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
            max-width: 100%;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin: 0 0 10px 0;
        }
        
        .form-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            background: #f8fafc;
            padding: 12px 15px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        
        .form-info div {
            flex: 1;
            margin: 0 5px;
        }
        
        .form-info strong {
            color: #1e40af;
        }
        
        .table-section {
            margin-bottom: 25px;
        }
        
        .table-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 12px;
            padding: 10px 12px;
            background: #dbeafe;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto 18px auto;
            page-break-inside: avoid;
            table-layout: fixed;
            word-wrap: break-word;
            font-size: 11px;
        }
        
        th {
            background: #3b82f6;
            color: white;
            font-weight: bold;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #2563eb;
            word-wrap: break-word;
            overflow-wrap: break-word;
            font-size: 10px;
            box-sizing: border-box;
        }
        
        td {
            padding: 8px 8px;
            border: 1px solid #e5e7eb;
            text-align: left;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 0;
            font-size: 10px;
            box-sizing: border-box;
        }
        
        tr:nth-child(even) {
            background: #f8fafc;
        }
        
        tr:hover {
            background: #dbeafe;
        }
        
        .approval-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .approval-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 12px;
            padding: 10px 12px;
            background: #dbeafe;
            border-radius: 6px;
            border-left: 4px solid #3b82f6;
        }
        
        .approval-table th {
            background: #3b82f6;
            border: 1px solid #2563eb;
        }
        
        .status-accept {
            color: #059669;
            font-weight: bold;
        }
        
        .status-reject {
            color: #dc2626;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        
        .no-data {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 20px;
        }
        
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/logo_futami.png'))) }}" alt="Logo Futami" style="height: 60px; margin-right: 20px;">
            <div style="text-align: center;">
                <h1 style="margin: 0; font-size: 24px; color: #1e40af;">{{ $kimia_form->title }}</h1>
                <div style="font-size: 14px; color: #6b7280; margin-top: 5px;">
                    <strong>No Form:</strong> {{ $kimia_form->no }}
                </div>
            </div>
        </div>
    </div>

    <div class="form-info">
        <div>
            <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($kimia_form->tanggal)->format('d/m/Y') }}
        </div>
    </div>

    @foreach($tables as $table)
    <div class="table-section">
        <div class="table-title">{{ $table->name }}</div>
        
        @if($table->entries->count() > 0)
            <table>
                <thead>
                    <tr>
                        @foreach($table->columns as $col)
                            <th>{{ $col->nama_kolom }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($table->entries as $entry)
                        <tr>
                            @foreach($table->columns as $col)
                                <td>
                                    @if(isset($entry->data[$col->id]))
                                        @if($col->tipe_kolom === 'date')
                                            {{ \Carbon\Carbon::parse($entry->data[$col->id])->format('d/m/Y') }}
                                        @elseif($col->tipe_kolom === 'time')
                                            {{ $entry->data[$col->id] }}
                                        @elseif($col->tipe_kolom === 'decimal')
                                            {{ number_format($entry->data[$col->id], 2) }}
                                        @elseif($col->tipe_kolom === 'integer')
                                            {{ number_format($entry->data[$col->id]) }}
                                        @else
                                            {{ $entry->data[$col->id] }}
                                        @endif
                                    @else
                                        <span style="color: #9ca3af;">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Tidak ada data entry untuk tabel ini
            </div>
        @endif
    </div>
    @endforeach

    <div class="approval-section">
        <div class="approval-title">Approval / Signature</div>
        
        @if($signatures->count() > 0)
            <table class="approval-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($signatures as $sig)
                        <tr>
                            <td>{{ $sig->name ?? '-' }}</td>
                            <td>{{ $sig->jabatan ?? '-' }}</td>
                            <td class="{{ $sig->status === 'accept' ? 'status-accept' : 'status-reject' }}">
                                {{ ucfirst($sig->status) }}
                            </td>
                            <td>{{ $sig->tanggal ? \Carbon\Carbon::parse($sig->tanggal)->format('d/m/Y') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Belum ada approval/signature
            </div>
        @endif
        
        <div style="text-align: right; margin-top: 20px; font-weight: bold; color: #1e40af;">
            No. Dokumen: {{ $kimia_form->no_dokumen ?? '-' }}
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem LIMIT-X</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
