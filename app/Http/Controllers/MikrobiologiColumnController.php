<?php

namespace App\Http\Controllers;

use App\Models\MikrobiologiColumn;
use Illuminate\Http\Request;

class MikrobiologiColumnController extends Controller
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
            'nama_kolom' => 'required|string',
            'tipe_kolom' => 'required|in:string,integer,decimal,date,time',
            'urutan' => 'nullable|integer',
        ]);
        $col = MikrobiologiColumn::create($validated);
        return response()->json($col);
    }

    /**
     * Display the specified resource.
     */
    public function show(MikrobiologiColumn $mikrobiologiColumn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MikrobiologiColumn $mikrobiologiColumn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $col = MikrobiologiColumn::findOrFail($id);
        $validated = $request->validate([
            'nama_kolom' => 'required|string',
            'tipe_kolom' => 'required|in:string,integer,decimal,date,time',
            'urutan' => 'nullable|integer',
        ]);
        $col->update($validated);
        return response()->json($col);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $col = MikrobiologiColumn::findOrFail($id);
        $col->delete();
        return response()->json(['success' => true]);
    }
}
