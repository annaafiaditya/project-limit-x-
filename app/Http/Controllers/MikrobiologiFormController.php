<?php

namespace App\Http\Controllers;

use App\Models\MikrobiologiForm;
use App\Models\MikrobiologiObservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\FormExport;
use App\Exports\MikrobiologiCombinedExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class MikrobiologiFormController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $search_tgl = $request->input('search_tgl');
        $group_title = $request->input('group_title');
        $perPage = $request->input('perPage', 10);
        $query = MikrobiologiForm::query();
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('no', 'like', "%$search%")
                    ->orWhere('tgl_inokulasi', 'like', "%$search%")
                    ->orWhere('tgl_pengamatan', 'like', "%$search%")
                ;
            });
        }
        if ($search_tgl) {
            $query->where(function($q) use ($search_tgl) {
                $q->whereDate('tgl_inokulasi', $search_tgl)
                    ->orWhereDate('tgl_pengamatan', $search_tgl);
            });
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
        
        $forms = $query->with(['entries', 'signatures'])->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));
        $titles = Cache::remember('mikro_distinct_titles', 120, function(){
            return MikrobiologiForm::select('title')->distinct()->orderBy('title')->pluck('title');
        });
        $template_titles = $titles;
        return view('mikrobiologi_forms.index', compact('forms', 'search', 'search_tgl', 'group_title', 'titles', 'perPage', 'template_titles'));
    }

    public function create(Request $request)
    {
        $template = null;
        $columns = collect();
        $suggested_no = '';
        
        if ($request->has('template_title')) {
            $template = \App\Models\MikrobiologiForm::where('title', $request->template_title)
                ->with(['columns', 'entries'])
                ->latest()->first();
            if ($template) {
                $columns = $template->columns()->orderBy('urutan')->get();
                

                $lastFormWithSameTitle = MikrobiologiForm::where('title', $template->title)
                    ->orderBy('created_at', 'desc')
                    ->first();
                

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
        return view('mikrobiologi_forms.create', compact('template', 'columns', 'suggested_no'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'no' => 'required',
            'judul_tabel' => 'nullable|string',
            'tgl_inokulasi' => 'required|date',

            'tgl_pengamatan' => 'nullable|date',
            'observations' => 'nullable|array',
            'observations.*.tanggal' => 'required_with:observations|date',
            'observations.*.keterangan' => 'nullable|string',
        ]);

        $data = [
            'title' => $validated['title'],
            'no' => $validated['no'],
            'judul_tabel' => $validated['judul_tabel'] ?? null,
            'tgl_inokulasi' => $validated['tgl_inokulasi'],
        ];

        $firstObsDate = null;
        if ($request->filled('observations')) {
            foreach ($request->input('observations') as $obs) {
                if (!empty($obs['tanggal'])) { $firstObsDate = $obs['tanggal']; break; }
            }
        }
        $data['tgl_pengamatan'] = $validated['tgl_pengamatan'] ?? $firstObsDate ?? $validated['tgl_inokulasi'];
        $data['created_by'] = Auth::id();
        $form = MikrobiologiForm::create($data);

        if ($request->filled('observations')) {
            foreach ($request->input('observations') as $obs) {
                if (!empty($obs['tanggal'])) {
                    MikrobiologiObservation::create([
                        'form_id' => $form->id,
                        'tanggal' => $obs['tanggal'],
                        'keterangan' => $obs['keterangan'] ?? null,
                    ]);
                }
            }
            $first = collect($request->input('observations'))->filter(function($o){ return !empty($o['tanggal']); })->first();
            if ($first && empty($validated['tgl_pengamatan'])) {
                $form->tgl_pengamatan = $first['tanggal'];
                $form->save();
            }
        }

        if ($request->has('template_title')) {
            \Log::info('DEBUG STORE: template_title', [$request->template_title]);
            $template = \App\Models\MikrobiologiForm::where('title', $request->template_title)->with(['columns', 'entries'])->latest()->first();
            if ($template) {

                $title = $validated['title'];
                $lastFormWithSameTitle = MikrobiologiForm::where('title', $title)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
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
                
                $newNo = $nextNumber . '/' . $jenis . '/' . $romanMonth . '/' . $currentYear;
                $form->no = $newNo;
                $form->save();
                \Log::info('DEBUG STORE: no form otomatis generated', ['form_id' => $form->id, 'new_no' => $newNo]);
                
                $latestForm = \App\Models\MikrobiologiForm::whereNotNull('no_dokumen')
                    ->where('no_dokumen', '!=', '')
                    ->orderBy('updated_at', 'desc')
                    ->first();
                
                if ($latestForm && !empty($latestForm->no_dokumen)) {
                    $form->no_dokumen = $latestForm->no_dokumen;
                    $form->save();
                    \Log::info('DEBUG STORE: no_dokumen diambil dari form terakhir', ['form_id' => $form->id, 'no_dokumen' => $form->no_dokumen, 'source_form_id' => $latestForm->id]);
                } else {
                    \Log::info('DEBUG STORE: tidak ada form dengan no_dokumen');
                }
                \Log::info('DEBUG STORE: template_id', [$template->id]);
                \Log::info('DEBUG STORE: template columns', $template->columns()->get()->toArray());
                foreach ($template->columns()->get() as $col) {
                    try {
                        \Log::info('Akan create kolom duplikat', ['form_id' => $form->id, 'col' => $col->toArray()]);
                        $newCol = \App\Models\MikrobiologiColumn::create([
                            'form_id' => $form->id,
                            'nama_kolom' => $col->nama_kolom,
                            'tipe_kolom' => $col->tipe_kolom,
                            'urutan' => $col->urutan,
                        ]);
                        \Log::info('Berhasil create kolom duplikat', ['newCol' => $newCol->toArray()]);
                    } catch (\Exception $e) {
                        \Log::error('Gagal create kolom duplikat: ' . $e->getMessage(), ['col' => $col->toArray(), 'form_id' => $form->id]);
                        abort(500, 'Gagal create kolom duplikat: ' . $e->getMessage());
                    }
                }
                \Log::info('DEBUG STORE: template entries count', [count($template->entries)]);
                foreach ($template->entries as $entry) {
                    \Log::info('DEBUG STORE: duplicating entry', ['entry_id' => $entry->id, 'data' => $entry->data]);
                    $newEntry = $form->entries()->create([
                        'data' => $entry->data,
                    ]);
                    \Log::info('DEBUG STORE: entry created', ['new_entry_id' => $newEntry->id]);
                }
            }
        }
        // Logic duplikat form jika dari template
        if ($request->has('columns.nama_kolom')) {
            $nama_kolom = $request->input('columns.nama_kolom');
            $tipe_kolom = $request->input('columns.tipe_kolom');
            $urutan = $request->input('columns.urutan');
            for ($i = 0; $i < count($nama_kolom); $i++) {
                \App\Models\MikrobiologiColumn::create([
                    'form_id' => $form->id,
                    'nama_kolom' => $nama_kolom[$i],
                    'tipe_kolom' => $tipe_kolom[$i],
                    'urutan' => $urutan[$i] ?? 0,
                ]);
            }
        }
        return redirect()->route('mikrobiologi-forms.show', ['mikrobiologi_form' => $form->id])->with('success', 'Form berhasil dibuat!');
    }

    public function show(MikrobiologiForm $mikrobiologi_form)
    {
        $columns = $mikrobiologi_form->columns()->orderBy('urutan')->get();
        $entries = $mikrobiologi_form->entries()->orderBy('id')->get();
        $signatures = $mikrobiologi_form->signatures()->get()->keyBy('role');
        return view('mikrobiologi_forms.show', [
            'form' => $mikrobiologi_form,
            'columns' => $columns,
            'entries' => $entries,
            'signatures' => $signatures,
        ]);
    }

    public function edit(MikrobiologiForm $mikrobiologi_form)
    {
        return view('mikrobiologi_forms.edit', ['form' => $mikrobiologi_form]);
    }

    public function update(Request $request, MikrobiologiForm $mikrobiologi_form)
    {
        $validated = $request->validate([
            'title' => 'required',
            'no' => 'required',
            'judul_tabel' => 'nullable|string',
            'tgl_inokulasi' => 'required|date',
            'tgl_pengamatan' => 'nullable|date',
            'observations' => 'nullable|array',
            'observations.*.id' => 'nullable|integer',
            'observations.*.tanggal' => 'required_with:observations|date',
            'observations.*.keterangan' => 'nullable|string',
        ]);
        // Build safe payload ensuring non-null tgl_pengamatan
        $payload = [
            'title' => $validated['title'],
            'no' => $validated['no'],
            'judul_tabel' => $validated['judul_tabel'] ?? null,
            'tgl_inokulasi' => $validated['tgl_inokulasi'],
        ];
        $firstObsDate = null;
        if ($request->has('observations')) {
            foreach ($request->input('observations') as $obs) {
                if (!empty($obs['tanggal'])) { $firstObsDate = $obs['tanggal']; break; }
            }
        }
        $payload['tgl_pengamatan'] = $validated['tgl_pengamatan'] ?? $firstObsDate ?? $validated['tgl_inokulasi'];
        $mikrobiologi_form->update($payload);

        if ($request->has('observations')) {
            $idsKept = [];
            foreach ($request->input('observations') as $obs) {
                if (!empty($obs['id'])) {
                    $model = MikrobiologiObservation::where('form_id', $mikrobiologi_form->id)->where('id', $obs['id'])->first();
                    if ($model) {
                        $model->update([
                            'tanggal' => $obs['tanggal'],
                            'keterangan' => $obs['keterangan'] ?? null,
                        ]);
                        $idsKept[] = $model->id;
                    }
                } else if (!empty($obs['tanggal'])) {
                    $model = MikrobiologiObservation::create([
                        'form_id' => $mikrobiologi_form->id,
                        'tanggal' => $obs['tanggal'],
                        'keterangan' => $obs['keterangan'] ?? null,
                    ]);
                    $idsKept[] = $model->id;
                }
            }
            MikrobiologiObservation::where('form_id', $mikrobiologi_form->id)->whereNotIn('id', $idsKept)->delete();

            $first = MikrobiologiObservation::where('form_id', $mikrobiologi_form->id)->orderBy('tanggal')->first();
            $mikrobiologi_form->tgl_pengamatan = $validated['tgl_pengamatan'] ?? ($first->tanggal ?? $validated['tgl_inokulasi']);
            $mikrobiologi_form->save();
        }

        if ($request->query('from') === 'show') {
            return redirect()->route('mikrobiologi-forms.show', ['mikrobiologi_form' => $mikrobiologi_form->id])->with('success', 'Form berhasil diupdate!');
        }

        return redirect()->route('mikrobiologi-forms.index')->with('success', 'Form berhasil diupdate!');
    }

    public function destroy(MikrobiologiForm $mikrobiologi_form)
    {
        $mikrobiologi_form->delete();
        return redirect()->route('mikrobiologi-forms.index')->with('info', 'Data form yang dihapus akan ke sampah, lokasinya di dropdown profile');
    }

    public function uniqueTitles()
    {
        $titles = \App\Models\MikrobiologiForm::select('title')->distinct()->orderBy('title')->get();
        return response()->json($titles);
    }

    public function export(MikrobiologiForm $mikrobiologi_form)
    {
        $judul = preg_replace('/[^A-Za-z0-9_\-]/', '_', $mikrobiologi_form->title);
        $no = preg_replace('/[^A-Za-z0-9_\-]/', '_', $mikrobiologi_form->no);
        $filename = $judul.'_'.$no.'.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\FormExport($mikrobiologi_form), $filename);
    }

    public function exportAll(Request $request)
    {
        $query = MikrobiologiForm::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('no', 'like', "%$search%")
                    ->orWhere('tgl_inokulasi', 'like', "%$search%")
                    ->orWhere('tgl_pengamatan', 'like', "%$search%");
            });
        }

        if ($request->filled('search_tgl')) {
            $search_tgl = $request->input('search_tgl');
            $query->where(function($q) use ($search_tgl) {
                $q->whereDate('tgl_inokulasi', $search_tgl)
                    ->orWhereDate('tgl_pengamatan', $search_tgl);
            });
        }

        if ($request->filled('group_title')) {
            $query->where('title', $request->input('group_title'));
        }

        if ($request->input('approval') === 'pending') {
            $query->whereHas('signatures', function($q){ $q->where('status', 'accept'); }, '<', 3);
        }

        $ids = $query->pluck('id')->toArray();

        if (empty($ids)) {
            return back()->with('export_error', 'Tidak ada data sesuai filter untuk diexport.');
        }


        $filenameParts = ['Mikrobiologi'];

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
        }
        
        $filenameParts[] = now()->format('Ymd_His');
        $filename = implode('_', $filenameParts) . '.xlsx';
        
        return Excel::download(new MikrobiologiCombinedExport($ids), $filename);
    }

    public function exportPdf(MikrobiologiForm $mikrobiologi_form)
    {
        $columns = $mikrobiologi_form->columns()->orderBy('urutan')->get();
        $entries = $mikrobiologi_form->entries()->orderBy('id')->get();
        $signatures = $mikrobiologi_form->signatures()->get()->sortBy(function($sig) {
            $order = ['technician' => 1, 'staff' => 2, 'supervisor' => 3];
            return $order[$sig->role] ?? 4;
        });
        $observations = $mikrobiologi_form->observations()->orderBy('tanggal')->get();
        
        $judul = preg_replace('/[^A-Za-z0-9_\-]/', '_', $mikrobiologi_form->title);
        $no = preg_replace('/[^A-Za-z0-9_\-]/', '_', $mikrobiologi_form->no);
        $filename = $judul.'_'.$no.'.pdf';

        $html = view('mikrobiologi_forms.pdf', compact('mikrobiologi_form', 'columns', 'entries', 'signatures', 'observations'))->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    public function updateNoDokumen(Request $request, MikrobiologiForm $mikrobiologi_form)
    {
        $validated = $request->validate([
            'no_dokumen' => 'nullable|string|max:255',
        ]);
        $mikrobiologi_form->update($validated);
        return redirect()->route('mikrobiologi-forms.show', ['mikrobiologi_form' => $mikrobiologi_form->id])
            ->with('success', 'No. Dokumen berhasil disimpan');
    }
}
