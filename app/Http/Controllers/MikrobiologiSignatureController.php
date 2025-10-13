<?php

namespace App\Http\Controllers;

use App\Models\MikrobiologiSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MikrobiologiSignatureController extends Controller
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
            'role' => 'required|in:technician,staff,supervisor',
            'jabatan' => 'required|string',
            'status' => 'required|in:accept,reject',
            'name' => 'required|string',
            'tanggal' => 'required|date',
            'alasan' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        // Cek apakah user bisa approve role ini
        if (!$user->canApprove($validated['role'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk approve role ini!');
        }

        MikrobiologiSignature::updateOrCreate(
            ['form_id' => $validated['form_id'], 'role' => $validated['role']],
            $validated
        );
        return redirect()->route('mikrobiologi-forms.show', ['mikrobiologi_form' => $validated['form_id']])->with('success', 'Status berhasil disimpan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(MikrobiologiSignature $mikrobiologiSignature)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MikrobiologiSignature $mikrobiologiSignature)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MikrobiologiSignature $mikrobiologiSignature)
    {
        $validated = $request->validate([
            'jabatan' => 'required|in:QA Lab. Technician,QA Staff,QA Supervisor',
            'status' => 'required|in:accept,reject',
            'name' => 'required|string',
            'tanggal' => 'required|date',
            'alasan' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        // Cek apakah user bisa approve role ini
        if (!$user->canApprove($mikrobiologiSignature->role)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk approve role ini!');
        }

        $mikrobiologiSignature->update($validated);
        return redirect()->route('mikrobiologi-forms.show', ['mikrobiologi_form' => $mikrobiologiSignature->form_id])->with('success', 'Status berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MikrobiologiSignature $mikrobiologiSignature)
    {
        $user = Auth::user();
        
        // Cek apakah user bisa approve role ini
        if (!$user->canApprove($mikrobiologiSignature->role)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus signature ini!');
        }

        $formId = $mikrobiologiSignature->form_id;
        $mikrobiologiSignature->delete();
        return redirect()->route('mikrobiologi-forms.show', ['mikrobiologi_form' => $formId])->with('success', 'Signature berhasil dihapus!');
    }
}
