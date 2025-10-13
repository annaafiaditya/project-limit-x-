<?php

namespace App\Http\Controllers;

use App\Models\MikrobiologiEntry;
use Illuminate\Http\Request;

class MikrobiologiEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id' => 'required|exists:mikrobiologi_forms,id',
            'data' => 'required|array',
        ]);
        $entry = MikrobiologiEntry::create($validated);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($entry);
        }
        return redirect()->route('mikrobiologi-forms.show', ['mikrobiologi_form' => $request->form_id])->with('success', 'Data entry berhasil ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MikrobiologiEntry $mikrobiologiEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MikrobiologiEntry $mikrobiologiEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MikrobiologiEntry $mikrobiologiEntry)
    {
        try {
            $validated = $request->validate([
                'data' => 'required|array',
            ]);
            $mikrobiologiEntry->update($validated);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'updated' => true]);
            }
            return redirect()->route('mikrobiologi-forms.show', ['mikrobiologi_form' => $mikrobiologiEntry->form_id])->with('success', 'Data entry berhasil diupdate!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal update entry: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal update entry!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, MikrobiologiEntry $mikrobiologiEntry)
    {
        try {
            $mikrobiologiEntry->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true]);
            }
            return redirect()->back()->with('success', 'Data entry berhasil dihapus!');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal hapus entry: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal hapus entry!');
        }
    }
}
