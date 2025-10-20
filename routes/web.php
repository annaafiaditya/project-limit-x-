<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MikrobiologiFormController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\MikrobiologiSignatureController;
use App\Http\Controllers\MikrobiologiColumnController;
use App\Http\Controllers\MikrobiologiEntryController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('home');
});

Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dashboard/data', function () {
        $judulCounts = Cache::remember('dash_mikro_judul_counts', 60, function () {
            return \App\Models\MikrobiologiForm::select('title', DB::raw('COUNT(*) as total'))
                ->groupBy('title')
                ->orderBy('title')
                ->pluck('total', 'title');
        });

        $entryCount = Cache::remember('dash_mikro_entry_count', 60, function () {
            return \App\Models\MikrobiologiEntry::count();
        });

        $approvalPending = Cache::remember('dash_mikro_approval_pending', 60, function () {
            return \App\Models\MikrobiologiForm::whereDoesntHave('signatures', function($q){
                $q->where('status', 'accept');
            })->orWhereHas('signatures', function($q){
                $q->where('status', 'accept');
            }, '<', 3)->count();
        });

        $kimiaJudulCounts = Cache::remember('dash_kimia_judul_counts', 60, function () {
            return \App\Models\KimiaForm::select('title', DB::raw('COUNT(*) as total'))
                ->groupBy('title')
                ->orderBy('title')
                ->pluck('total', 'title');
        });

        $kimiaEntryCount = Cache::remember('dash_kimia_entry_count', 60, function () {
            return \App\Models\KimiaEntry::count();
        });

        $kimiaApprovalPending = Cache::remember('dash_kimia_approval_pending', 60, function () {
            return \App\Models\KimiaForm::whereDoesntHave('signatures', function($q){
                $q->where('status', 'accept');
            })->orWhereHas('signatures', function($q){
                $q->where('status', 'accept');
            }, '<', 3)->count();
        });

        $totalMikrobiologiForms = Cache::remember('dash_total_mikro_forms', 60, function () {
            return \App\Models\MikrobiologiForm::count();
        });

        $totalKimiaForms = Cache::remember('dash_total_kimia_forms', 60, function () {
            return \App\Models\KimiaForm::count();
        });

        return response()->json([
            'judul_labels' => collect($judulCounts)->keys()->values(),
            'judul_data' => collect($judulCounts)->values(),
            'entry_count' => $entryCount,
            'approval_pending' => $approvalPending,
            'total_forms' => $totalMikrobiologiForms,

            'kimia_judul_labels' => collect($kimiaJudulCounts)->keys()->values(),
            'kimia_judul_data' => collect($kimiaJudulCounts)->values(),
            'kimia_entry_count' => $kimiaEntryCount,
            'kimia_approval_pending' => $kimiaApprovalPending,
            'kimia_total_forms' => $totalKimiaForms,
        ]);
    })->name('dashboard.data');

    Route::post('/dashboard/note', function (Illuminate\Http\Request $request) {
        $request->validate([
            'note' => 'nullable|string|max:2000',
        ]);
        $user = Auth::user();
        $user->note = $request->note;
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan pribadi berhasil disimpan'
            ]);
        }

        return redirect()->route('dashboard')->with('note_saved', true);
    })->name('dashboard.note');

    Route::get('/general-note', [App\Http\Controllers\GeneralNoteSimpleController::class, 'show'])->name('general-note.show');
    Route::post('/general-note', [App\Http\Controllers\GeneralNoteSimpleController::class, 'update'])->name('general-note.update');

    Route::get('/mikrobiologi-forms/export-all', [App\Http\Controllers\MikrobiologiFormController::class, 'exportAll'])->name('mikrobiologi-forms.export-all');
    Route::get('/mikrobiologi-forms/{mikrobiologi_form}/export', [App\Http\Controllers\MikrobiologiFormController::class, 'export'])->whereNumber('mikrobiologi_form')->name('mikrobiologi-forms.export');
    Route::get('/mikrobiologi-forms/{mikrobiologi_form}/export-pdf', [App\Http\Controllers\MikrobiologiFormController::class, 'exportPdf'])->whereNumber('mikrobiologi_form')->name('mikrobiologi-forms.export-pdf');

    // catatan error disini di bagian microbiologii
    Route::get('/mikrobiologi-forms/create', [App\Http\Controllers\MikrobiologiFormController::class, 'create'])->name('mikrobiologi-forms.create')->middleware('guest.access');
    Route::post('/mikrobiologi-forms', [App\Http\Controllers\MikrobiologiFormController::class, 'store'])->name('mikrobiologi-forms.store')->middleware('guest.access');
    Route::get('/mikrobiologi-forms/{mikrobiologi_form}/edit', [App\Http\Controllers\MikrobiologiFormController::class, 'edit'])->name('mikrobiologi-forms.edit')->middleware('guest.access');
    Route::put('/mikrobiologi-forms/{mikrobiologi_form}', [App\Http\Controllers\MikrobiologiFormController::class, 'update'])->name('mikrobiologi-forms.update')->middleware('guest.access');
    Route::delete('/mikrobiologi-forms/{mikrobiologi_form}', [App\Http\Controllers\MikrobiologiFormController::class, 'destroy'])->name('mikrobiologi-forms.destroy')->middleware('guest.access');

    Route::resource('mikrobiologi-forms', MikrobiologiFormController::class)
        ->except(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('guest.access');

    Route::resource('mikrobiologi-forms.signatures', MikrobiologiSignatureController::class)->shallow()->middleware('guest.access');

    Route::put('/mikrobiologi-forms/{mikrobiologi_form}/no-dokumen', [App\Http\Controllers\MikrobiologiFormController::class, 'updateNoDokumen'])->name('mikrobiologi-forms.no-dokumen.update')->middleware('guest.access');

    Route::post('/columns', [MikrobiologiColumnController::class, 'store'])->name('columns.store')->middleware('guest.access');
    Route::put('/columns/{id}', [MikrobiologiColumnController::class, 'update'])->name('columns.update')->middleware('guest.access');
    Route::delete('/columns/{id}', [MikrobiologiColumnController::class, 'destroy'])->name('columns.destroy')->middleware('guest.access');

    Route::post('/mikrobiologi-forms/{form}/entries', [MikrobiologiEntryController::class, 'store'])->name('mikrobiologi-forms.entries.store')->middleware('guest.access');
    Route::delete('/entries/{mikrobiologiEntry}', [MikrobiologiEntryController::class, 'destroy'])->name('entries.destroy')->middleware('guest.access');
    Route::put('/entries/{mikrobiologiEntry}', [MikrobiologiEntryController::class, 'update'])->name('entries.update')->middleware('guest.access');

    Route::get('/template-forms/unique-titles', [App\Http\Controllers\MikrobiologiFormController::class, 'uniqueTitles'])->name('template-forms.unique-titles');

    Route::get('/mikrobiologi-forms', function (Illuminate\Http\Request $request) {
        $search = $request->input('search');
        $search_tgl = $request->input('search_tgl');
        $group_title = $request->input('group_title');
        $perPage = $request->input('perPage', 10);

        $query = \App\Models\MikrobiologiForm::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('no', 'like', "%$search%")
                  ->orWhere('tgl_inokulasi', 'like', "%$search%")
                  ->orWhere('tgl_pengamatan', 'like', "%$search%");
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

        $forms = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));

        $titles = \App\Models\MikrobiologiForm::select('title')->distinct()->orderBy('title')->pluck('title');
        $template_titles = $titles;

        return view('mikrobiologi_forms.index', compact('forms', 'search', 'search_tgl', 'group_title', 'titles', 'perPage', 'template_titles'));
    })->name('mikrobiologi-forms.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/kimia', function (Illuminate\Http\Request $request) {
        $search = $request->input('search');
        $search_tgl = $request->input('search_tgl');
        $group_title = $request->input('group_title');
        $perPage = $request->input('perPage', 10);
        $query = \App\Models\KimiaForm::query();

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

        $forms = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends($request->except('page'));

        $titles = \App\Models\KimiaForm::select('title')->distinct()->orderBy('title')->pluck('title');
        $template_titles = $titles;

        return view('kimia_forms.index', compact('forms', 'search', 'search_tgl', 'group_title', 'titles', 'perPage', 'template_titles'));
    })->name('kimia.index');

    Route::get('/kimia/create', [App\Http\Controllers\KimiaController::class, 'create'])->name('kimia.create')->middleware('guest.access');
    Route::post('/kimia', [App\Http\Controllers\KimiaController::class, 'store'])->name('kimia.store')->middleware('guest.access');
    Route::get('/kimia/{kimia_form}', [App\Http\Controllers\KimiaController::class, 'show'])->whereNumber('kimia_form')->name('kimia.show');
    Route::get('/kimia/{kimia_form}/edit', [App\Http\Controllers\KimiaController::class, 'edit'])->whereNumber('kimia_form')->name('kimia.edit')->middleware('guest.access');
    Route::put('/kimia/{kimia_form}', [App\Http\Controllers\KimiaController::class, 'update'])->whereNumber('kimia_form')->name('kimia.update')->middleware('guest.access');
    Route::delete('/kimia/{kimia_form}', [App\Http\Controllers\KimiaController::class, 'destroy'])->whereNumber('kimia_form')->name('kimia.destroy')->middleware('guest.access');
    Route::post('/kimia/{kimia_form}/tables', [App\Http\Controllers\KimiaController::class, 'addTable'])->whereNumber('kimia_form')->name('kimia.tables.add')->middleware('guest.access');
    Route::put('/kimia-tables/{kimiaTable}', [App\Http\Controllers\KimiaController::class, 'updateTable'])->name('kimia.tables.update')->middleware('guest.access');
    Route::delete('/kimia-tables/{kimiaTable}', [App\Http\Controllers\KimiaController::class, 'destroyTable'])->name('kimia.tables.destroy')->middleware('guest.access');

    Route::post('/kimia-columns', [App\Http\Controllers\KimiaController::class, 'storeColumn'])->name('kimia-columns.store')->middleware('guest.access');
    Route::put('/kimia-columns/{kimiaColumn}', [App\Http\Controllers\KimiaController::class, 'updateColumn'])->name('kimia-columns.update')->middleware('guest.access');
    Route::delete('/kimia-columns/{kimiaColumn}', [App\Http\Controllers\KimiaController::class, 'destroyColumn'])->name('kimia-columns.destroy')->middleware('guest.access');

    Route::post('/kimia-entries', [App\Http\Controllers\KimiaController::class, 'storeEntry'])->name('kimia-entries.store')->middleware('guest.access');
    Route::put('/kimia-entries/{kimiaEntry}', [App\Http\Controllers\KimiaController::class, 'updateEntry'])->name('kimia-entries.update')->middleware('guest.access');
    Route::delete('/kimia-entries/{kimiaEntry}', [App\Http\Controllers\KimiaController::class, 'destroyEntry'])->name('kimia-entries.destroy')->middleware('guest.access');

    Route::post('/kimia/{kimia_form}/signatures', [App\Http\Controllers\KimiaController::class, 'storeSignature'])->whereNumber('kimia_form')->name('kimia.signatures.store')->middleware('guest.access');

    Route::get('/kimia/export-all', [App\Http\Controllers\KimiaController::class, 'exportAll'])->name('kimia.export-all');
    Route::get('/kimia/{kimia_form}/export', [App\Http\Controllers\KimiaController::class, 'export'])->whereNumber('kimia_form')->name('kimia.export');
    Route::get('/kimia/{kimia_form}/export-pdf', [App\Http\Controllers\KimiaController::class, 'exportPdf'])->whereNumber('kimia_form')->name('kimia.export-pdf');

    Route::get('/kimia/{kimia_form}/print', function (App\Models\KimiaForm $kimia_form) {
        $tables = $kimia_form->tables()->with(['columns' => function($q){ $q->orderBy('urutan'); }, 'entries'])->get();
        $signatures = $kimia_form->signatures()->get()->sortBy(function($sig) {
            $order = ['technician' => 1, 'staff' => 2, 'supervisor' => 3];
            return $order[$sig->role] ?? 4;
        });
        return view('kimia_forms.print', compact('kimia_form', 'tables', 'signatures'));
    })->whereNumber('kimia_form')->name('kimia.print');

    Route::put('/kimia/{kimia_form}/no-dokumen', [App\Http\Controllers\KimiaController::class, 'updateNoDokumen'])->whereNumber('kimia_form')->name('kimia.no-dokumen.update');

    Route::get('/trash', [App\Http\Controllers\TrashController::class, 'index'])->name('trash.index')->middleware('guest.access');
    Route::patch('/trash/kimia/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreKimia'])->name('trash.restore-kimia')->middleware('guest.access');
    Route::patch('/trash/mikrobiologi/{id}/restore', [App\Http\Controllers\TrashController::class, 'restoreMikrobiologi'])->name('trash.restore-mikrobiologi')->middleware('guest.access');
    Route::delete('/trash/kimia/{id}/force-delete', [App\Http\Controllers\TrashController::class, 'forceDeleteKimia'])->name('trash.force-delete-kimia')->middleware('guest.access');
    Route::delete('/trash/mikrobiologi/{id}/force-delete', [App\Http\Controllers\TrashController::class, 'forceDeleteMikrobiologi'])->name('trash.force-delete-mikrobiologi')->middleware('guest.access');
});

require __DIR__.'/auth.php';
