<?php

namespace App\Http\Controllers;

use App\Models\KimiaForm;
use App\Models\KimiaColumn;
use App\Models\KimiaEntry;
use App\Models\KimiaSignature;
use App\Models\KimiaTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KimiaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $search_tgl = $request->input('search_tgl');
        $group_title = $request->input('group_title');
        $perPage = $request->input('perPage', 10);
        
        $query = KimiaForm::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('no', 'like', "%$search%")
                  ->orWhere('tanggal', 'like', "%$search%");
            });
        }
        
        if ($search_tgl) {
            $query->whereDate('tanggal', $search_tgl);
        }
        
        if ($group_title) {
            $query->where('title', $group_title);
        }

        if ($request->input('approval') === 'pending') {
            $query->whereHas('signatures', function($q){
                $q->where('status', 'accept');
            }, '<', 3);
        } elseif ($request->input('approval') === 'completed') {
            $query->whereHas('signatures', function($q){
                $q->where('status', 'accept');
            }, '=', 3);
        } elseif ($request->input('approval') === 'technician') {
            $query->whereHas('signatures', function($q){
                $q->where('role', 'technician')->where('status', 'accept');
            });
        } elseif ($request->input('approval') === 'staff') {
            $query->whereHas('signatures', function($q){
                $q->where('role', 'staff')->where('status', 'accept');
            });
        } elseif ($request->input('approval') === 'supervisor') {
            $query->whereHas('signatures', function($q){
                $q->where('role', 'supervisor')->where('status', 'accept');
            });
        }

        $forms = $query->with(['entries', 'signatures'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));
        
        $titles = Cache::remember('kimia_distinct_titles', 120, function(){
            return KimiaForm::select('title')->distinct()->orderBy('title')->pluck('title');
        });
        $template_titles = $titles;
        
        return view('kimia_forms.index', compact('forms', 'search', 'search_tgl', 'group_title', 'titles', 'perPage', 'template_titles'));
    }

    public function create(Request $request)
    {
        $template = null;
        $tables = collect();
        $suggested_no = '';
        
        if ($request->has('template_title')) {
            \Log::info('DEBUG KIMIA CREATE: template_title received', [$request->template_title]);
            $template = KimiaForm::where('title', $request->template_title)
                ->with(['tables.columns' => function($q){ $q->orderBy('urutan'); }, 'tables.entries'])
                ->latest()->first();
            \Log::info('DEBUG KIMIA CREATE: template found', ['template_id' => $template ? $template->id : 'null', 'tables_count' => $template ? count($template->tables) : 0]);
            if ($template) {
                $tables = $template->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }])->get();
                \Log::info('DEBUG KIMIA CREATE: tables loaded', ['tables_count' => $tables->count()]);

                $lastFormWithSameTitle = KimiaForm::where('title', $template->title)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                // 01/LAMK/V/25
                $currentMonth = date('n');
                $currentYear = date('y');
                $romanMonths = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                $romanMonth = $romanMonths[$currentMonth];
                
                if ($lastFormWithSameTitle) {
                    $lastNo = $lastFormWithSameTitle->no;
                    if (preg_match('/^(\d+)\//', $lastNo, $matches)) {
                        $nextNumber = str_pad(intval($matches[1]) + 1, 2, '0', STR_PAD_LEFT);
                    } else {
                        $nextNumber = '01';
                    }
                } else {
                    $nextNumber = '01';
                }

                $jenis = 'LAMK'; // default
                if ($lastFormWithSameTitle && preg_match('/\d+\/([A-Z]+)\//', $lastFormWithSameTitle->no, $matches)) {
                    $jenis = $matches[1];
                } elseif ($template && preg_match('/\d+\/([A-Z]+)\//', $template->no, $matches)) {
                    $jenis = $matches[1];
                }

                $suggested_no = $nextNumber . '/' . $jenis . '/' . $romanMonth . '/' . $currentYear;
            }
        }

        return view('kimia_forms.create', compact('template', 'tables', 'suggested_no'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'no' => 'required',
            'tanggal' => 'required|date',
        ]);
        
        $validated['created_by'] = Auth::id();
        $form = null;

        if ($request->has('template_title')) {
            \Log::info('DEBUG KIMIA STORE: template_title received', [$request->template_title]);
            $template = KimiaForm::where('title', $request->template_title)
                ->with(['tables.columns' => function($q){ $q->orderBy('urutan'); }, 'tables.entries'])
                ->latest()->first();
            \Log::info('DEBUG KIMIA STORE: template found', ['template_id' => $template ? $template->id : 'null', 'tables_count' => $template ? count($template->tables) : 0]);
            if ($template) {
                \Log::info('DEBUG KIMIA STORE: template details', [
                    'template_id' => $template->id,
                    'template_title' => $template->title,
                    'tables_count' => count($template->tables),
                    'tables' => $template->tables->map(function($table) {
                        return [
                            'id' => $table->id,
                            'name' => $table->name,
                            'columns_count' => count($table->columns),
                            'entries_count' => count($table->entries)
                        ];
                    })->toArray()
                ]);
                DB::transaction(function () use ($validated, $template, &$form) {
                    // 01/LAMK/V/25
                    $title = $validated['title'];
                    $lastFormWithSameTitle = KimiaForm::where('title', $title)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    //  01/LAMK/V/25
                    $currentMonth = date('n'); // 1-12
                    $currentYear = date('y'); // 2 digit tahun
                    $romanMonths = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                    $romanMonth = $romanMonths[$currentMonth];

                    if ($lastFormWithSameTitle) {
                        $lastNo = $lastFormWithSameTitle->no;
                        if (preg_match('/^(\d+)\//', $lastNo, $matches)) {
                            $nextNumber = str_pad(intval($matches[1]) + 1, 2, '0', STR_PAD_LEFT);
                        } else {
                            $nextNumber = '01';
                        }
                    } else {
                        $nextNumber = '01';
                    }

                    $jenis = 'LAMK'; // default
                    if ($lastFormWithSameTitle && preg_match('/\d+\/([A-Z]+)\//', $lastFormWithSameTitle->no, $matches)) {
                        $jenis = $matches[1];
                    } elseif ($template && preg_match('/\d+\/([A-Z]+)\//', $template->no, $matches)) {
                        $jenis = $matches[1];
                    }
                    
                    $validated['no'] = $nextNumber . '/' . $jenis . '/' . $romanMonth . '/' . $currentYear;
                    
                    $form = KimiaForm::create($validated);
                    $latestForm = KimiaForm::whereNotNull('no_dokumen')
                        ->where('no_dokumen', '!=', '')
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    if ($latestForm && !empty($latestForm->no_dokumen)) {
                        $form->no_dokumen = $latestForm->no_dokumen;
                        $form->save();
                        \Log::info('DEBUG KIMIA STORE: no_dokumen diambil dari form terakhir', ['form_id' => $form->id, 'no_dokumen' => $form->no_dokumen, 'source_form_id' => $latestForm->id]);
                    } else {
                        \Log::info('DEBUG KIMIA STORE: tidak ada form dengan no_dokumen');
                    }
                    if ($template->tables->isEmpty()) {
                        $defaultTable = KimiaTable::create([
                            'form_id' => $form->id,
                            'name' => 'Tabel 1',
                        ]);
                        \Log::info('DEBUG KIMIA STORE: template has no tables, created default table', ['new_table_id' => $defaultTable->id]);
                    } else {
                        foreach ($template->tables as $templateTable) {
                            \Log::info('DEBUG KIMIA STORE: duplicating table', ['table_name' => $templateTable->name, 'columns_count' => count($templateTable->columns), 'entries_count' => count($templateTable->entries)]);
                            $newTable = KimiaTable::create([
                                'form_id' => $form->id,
                                'name' => $templateTable->name,
                            ]);
                            \Log::info('DEBUG KIMIA STORE: table created', ['new_table_id' => $newTable->id, 'new_table_name' => $newTable->name]);
                            foreach ($templateTable->columns as $col) {
                                $newColumn = KimiaColumn::create([
                                    'form_id' => $form->id,
                                    'table_id' => $newTable->id,
                                    'nama_kolom' => $col->nama_kolom,
                                    'tipe_kolom' => $col->tipe_kolom,
                                    'urutan' => $col->urutan,
                                ]);
                                \Log::info('DEBUG KIMIA STORE: column created', ['new_column_id' => $newColumn->id, 'new_column_name' => $newColumn->nama_kolom]);
                            }
                        }
                    }
                });
                \Log::info('DEBUG KIMIA STORE: duplication completed', ['form_id' => $form->id, 'tables_duplicated' => $form->tables()->count()]);
            }
        } else {
            $form = KimiaForm::create($validated);
            $table = KimiaTable::create([
                'form_id' => $form->id,
                'name' => 'Tabel 1',
            ]);
            
            if ($request->has('columns.nama_kolom')) {
                $nama_kolom = $request->input('columns.nama_kolom');
                $tipe_kolom = $request->input('columns.tipe_kolom');
                $urutan = $request->input('columns.urutan');
                
                for ($i = 0; $i < count($nama_kolom); $i++) {
                    KimiaColumn::create([
                        'form_id' => $form->id,
                        'table_id' => $table->id,
                        'nama_kolom' => $nama_kolom[$i],
                        'tipe_kolom' => $tipe_kolom[$i],
                        'urutan' => $urutan[$i] ?? 0,
                    ]);
                }
            }
        }
        
        return redirect()->route('kimia.show', ['kimia_form' => $form->id])->with('success', 'Form berhasil dibuat!');
    }

    public function show(KimiaForm $kimia_form)
    {
        $tables = $kimia_form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
        $signatures = $kimia_form->signatures()->get()->keyBy('role');
        
        return view('kimia_forms.show', [
            'form' => $kimia_form,
            'tables' => $tables,
            'signatures' => $signatures,
        ]);
    }

    public function addTable(Request $request, KimiaForm $kimia_form)
    {
        $validated = $request->validate([
            'name' => 'required|string'
        ]);
        KimiaTable::create([
            'form_id' => $kimia_form->id,
            'name' => $validated['name']
        ]);
        return redirect()->route('kimia.show', $kimia_form)->with('success', 'Tabel berhasil ditambahkan');
    }

    public function updateTable(Request $request, KimiaTable $kimiaTable)
    {
        $validated = $request->validate([
            'name' => 'required|string'
        ]);
        
        $kimiaTable->update($validated);
        
        if ($request->wantsJson()) {
            return response()->json($kimiaTable);
        }
        return redirect()->route('kimia.show', ['kimia_form' => $kimiaTable->form_id])->with('success', 'Tabel berhasil diupdate!');
    }

    public function destroyTable(KimiaTable $kimiaTable)
    {
        $form_id = $kimiaTable->form_id;
        $kimiaTable->delete();
        
        return redirect()->route('kimia.show', ['kimia_form' => $form_id])->with('success', 'Tabel berhasil dihapus!');
    }

    public function storeColumn(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:kimia_forms,id',
            'table_id' => 'required|exists:kimia_tables,id',
            'nama_kolom' => 'required|string',
            'tipe_kolom' => 'required|in:string,integer,date,time,decimal',
            'jumlah_kolom' => 'nullable|integer|min:1|max:10',
            'urutan' => 'nullable|integer',
        ]);
        
        $jumlah_kolom = $validated['jumlah_kolom'] ?? 1;
        $created_columns = [];
        
        $lastColumn = KimiaColumn::where('table_id', $validated['table_id'])
            ->orderBy('urutan', 'desc')
            ->first();
        $nextUrutan = $lastColumn ? $lastColumn->urutan + 1 : 1;
        
        for ($i = 1; $i <= $jumlah_kolom; $i++) {
            $columnData = [
                'form_id' => $validated['form_id'],
                'table_id' => $validated['table_id'],
                'nama_kolom' => $jumlah_kolom > 1 ? $validated['nama_kolom'] . ' ' . $i : $validated['nama_kolom'],
                'tipe_kolom' => $validated['tipe_kolom'],
                'urutan' => $nextUrutan + $i - 1,
            ];
            
            $column = KimiaColumn::create($columnData);
            $created_columns[] = $column;
        }
        
        if ($request->wantsJson()) {
            return response()->json([
                'id' => $created_columns[0]->id,
                'nama_kolom' => $created_columns[0]->nama_kolom,
                'tipe_kolom' => $created_columns[0]->tipe_kolom,
                'jumlah_created' => count($created_columns),
                'columns' => collect($created_columns)->map(function($col) {
                    return [
                        'id' => $col->id,
                        'nama_kolom' => $col->nama_kolom,
                        'tipe_kolom' => $col->tipe_kolom
                    ];
                })
            ]);
        }
        return back()->with('success', $jumlah_kolom . ' kolom berhasil ditambahkan!');
    }

    public function updateColumn(Request $request, KimiaColumn $kimiaColumn)
    {
        $validated = $request->validate([
            'nama_kolom' => 'required|string',
            'tipe_kolom' => 'required|in:string,integer,date,time,decimal',
            'urutan' => 'nullable|integer',
        ]);
        
        $kimiaColumn->update($validated);
        
        if ($request->wantsJson()) {
            return response()->json($kimiaColumn);
        }
        return back()->with('success', 'Kolom berhasil diupdate!');
    }

    public function destroyColumn(KimiaColumn $kimiaColumn)
    {
        $kimiaColumn->delete();
        
        return back()->with('success', 'Kolom berhasil dihapus!');
    }

    public function storeEntry(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:kimia_forms,id',
            'table_id' => 'required|exists:kimia_tables,id',
            'data' => 'required|array',
        ]);
        
        $entry = KimiaEntry::create($validated);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($entry);
        }
        
        return back()->with('success', 'Data entry berhasil ditambah!');
    }

    public function updateEntry(Request $request, KimiaEntry $kimiaEntry)
    {
        try {
            $validated = $request->validate([
                'data' => 'required|array',
            ]);
            $kimiaEntry->update($validated);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'updated' => true]);
            }
            return redirect()->route('kimia.show', ['kimia_form' => $kimiaEntry->form_id])->with('success', 'Data entry berhasil diupdate!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal update entry: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal update entry!');
        }
    }

    public function destroyEntry(KimiaEntry $kimiaEntry)
    {
        $form_id = $kimiaEntry->form_id;
        $kimiaEntry->delete();
        
        return redirect()->route('kimia.show', ['kimia_form' => $form_id])->with('success', 'Data entry berhasil dihapus!');
    }

    public function storeSignature(Request $request, KimiaForm $kimia_form)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:kimia_forms,id',
            'role' => 'required|in:technician,staff,supervisor',
            'name' => 'required|string',
            'jabatan' => 'required|string',
            'status' => 'required|in:accept,reject',
            'tanggal' => 'required|date',
        ]);

        $user = Auth::user();
        
        if (!$user->canApprove($validated['role'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk approve role ini!');
        }
        
        KimiaSignature::create($validated);
        
        return redirect()->route('kimia.show', ['kimia_form' => $kimia_form->id])->with('success', 'Signature berhasil disimpan!');
    }

    public function edit(KimiaForm $kimia_form)
    {
        return view('kimia_forms.edit', ['form' => $kimia_form]);
    }

    public function update(Request $request, KimiaForm $kimia_form)
    {
        $validated = $request->validate([
            'title' => 'required',
            'no' => 'required',
            'tanggal' => 'required|date',
        ]);
        
        $kimia_form->update($validated);
        
        if ($request->query('from') === 'show') {
            return redirect()->route('kimia.show', ['kimia_form' => $kimia_form->id])->with('success', 'Form berhasil diupdate!');
        }
        
        return redirect()->route('kimia.index')->with('success', 'Form berhasil diupdate!');
    }

    public function destroy(KimiaForm $kimia_form)
    {
        $kimia_form->delete();
        return redirect()->route('kimia.index')->with('info', 'Data form yang dihapus akan ke sampah, lokasinya di dropdown profile');
    }

    public function export(KimiaForm $kimia_form)
    {
        $judul = preg_replace('/[^A-Za-z0-9_\-]/', '_', $kimia_form->title);
        $no = preg_replace('/[^A-Za-z0-9_\-]/', '_', $kimia_form->no);
        $filename = $judul.'_'.$no.'.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KimiaFormExport($kimia_form), $filename);
    }

    public function exportAll(Request $request)
    {
        $query = KimiaForm::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('no', 'like', "%$search%")
                  ->orWhere('tanggal', 'like', "%$search%");
            });
        }

        if ($request->filled('search_tgl')) {
            $query->whereDate('tanggal', $request->input('search_tgl'));
        }

        if ($request->filled('group_title')) {
            $query->where('title', $request->input('group_title'));
        }

        if ($request->input('approval') === 'pending') {
            $query->whereHas('signatures', function($q){ $q->where('status', 'accept'); }, '<', 3);
        } elseif ($request->input('approval') === 'completed') {
            $query->whereHas('signatures', function($q){ $q->where('status', 'accept'); }, '=', 3);
        } elseif ($request->input('approval') === 'technician') {
            $query->whereHas('signatures', function($q){ $q->where('role', 'technician')->where('status', 'accept'); });
        } elseif ($request->input('approval') === 'staff') {
            $query->whereHas('signatures', function($q){ $q->where('role', 'staff')->where('status', 'accept'); });
        } elseif ($request->input('approval') === 'supervisor') {
            $query->whereHas('signatures', function($q){ $q->where('role', 'supervisor')->where('status', 'accept'); });
        }

        $ids = $query->pluck('id')->toArray();

        if (empty($ids)) {
            return back()->with('export_error', 'Tidak ada data sesuai filter untuk diexport.');
        }

        $filenameParts = ['Kimia'];
        
        if ($request->filled('search')) {
            $search = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->input('search'));
            $filenameParts[] = 'Search_' . substr($search, 0, 20);
        }
        
        if ($request->filled('search_tgl')) {
            $search_tgl = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->input('search_tgl'));
            $filenameParts[] = 'Tanggal_' . $search_tgl;
        }
        
        if ($request->filled('group_title')) {
            $group_title = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->input('group_title'));
            $filenameParts[] = 'Judul_' . substr($group_title, 0, 20);
        }
        
        if ($request->input('approval') === 'pending') {
            $filenameParts[] = 'Pending_Approval';
        } elseif ($request->input('approval') === 'completed') {
            $filenameParts[] = 'Completed_Approval';
        } elseif ($request->input('approval') === 'technician') {
            $filenameParts[] = 'Technician_Approval';
        } elseif ($request->input('approval') === 'staff') {
            $filenameParts[] = 'Staff_Approval';
        } elseif ($request->input('approval') === 'supervisor') {
            $filenameParts[] = 'Supervisor_Approval';
        }
        
        $filenameParts[] = now()->format('Ymd_His');
        $filename = implode('_', $filenameParts) . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KimiaCombinedExport($ids), $filename);
    }

    public function exportPdf(KimiaForm $kimia_form)
    {
        $tables = $kimia_form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
        $signatures = $kimia_form->signatures()->get()->sortBy(function($sig) {
            $order = ['technician' => 1, 'staff' => 2, 'supervisor' => 3];
            return $order[$sig->role] ?? 4;
        });
        
        $judul = preg_replace('/[^A-Za-z0-9_\-]/', '_', $kimia_form->title);
        $no = preg_replace('/[^A-Za-z0-9_\-]/', '_', $kimia_form->no);
        $filename = $judul.'_'.$no.'.pdf';
        
        $html = view('kimia_forms.pdf', compact('kimia_form', 'tables', 'signatures'))->render();
        
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    public function updateNoDokumen(Request $request, KimiaForm $kimia_form)
    {
        $validated = $request->validate([
            'no_dokumen' => 'nullable|string|max:255',
        ]);
        $kimia_form->update($validated);
        return redirect()->route('kimia.show', ['kimia_form' => $kimia_form->id])
            ->with('success', 'No. Dokumen berhasil disimpan');
    }
}
