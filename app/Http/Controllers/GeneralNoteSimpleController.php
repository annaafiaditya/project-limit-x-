<?php

namespace App\Http\Controllers;

use App\Models\GeneralNoteSimple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralNoteSimpleController extends Controller
{
    public function show()
    {
        $note = GeneralNoteSimple::getLatest();
        return response()->json([
            'content' => $note ? $note->content : '',
            'last_edited_by' => $note ? $note->last_edited_by : null,
            'last_edited_role' => $note ? $note->last_edited_role : null,
            'last_edited_at' => $note ? $note->last_edited_at : null,
            'last_editor_name' => $note && $note->lastEditor ? $note->lastEditor->name : null,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string|max:5000',
        ]);

        $user = Auth::user();

        $note = GeneralNoteSimple::getLatest();
        if (!$note) {
            $note = new GeneralNoteSimple();
        }

        $note->content = $request->input('content', '');
        $note->last_edited_by = $user->id;
        $note->last_edited_role = $user->role;
        $note->last_edited_at = now();
        $note->save();

        return response()->json([
            'success' => true,
            'message' => 'Catatan general berhasil disimpan',
            'last_edited_by' => $note->last_edited_by,
            'last_edited_role' => $note->last_edited_role,
            'last_edited_at' => $note->last_edited_at,
            'last_editor_name' => $user->name,
        ]);
    }
}
