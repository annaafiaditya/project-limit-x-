<?php

namespace App\Http\Controllers;

use App\Models\GeneralNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GeneralNoteController extends Controller
{
    /**
     * Get the latest general note
     */
    public function show()
    {
        try {
            $note = GeneralNote::getLatest();
            return response()->json([
                'content' => $note ? $note->content : '',
                'last_edited_by' => $note ? $note->last_edited_by : null,
                'last_edited_role' => $note ? $note->last_edited_role : null,
                'last_edited_at' => $note ? $note->last_edited_at : null,
                'last_editor_name' => $note && $note->lastEditor ? $note->lastEditor->name : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting general note: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat catatan'], 500);
        }
    }

    /**
     * Update or create general note
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'content' => 'nullable|string|max:5000',
            ]);

            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'User tidak terautentikasi'], 401);
            }
            
            DB::beginTransaction();
            
            try {
                // Get existing note or create new one
                $note = GeneralNote::getLatest();
                if (!$note) {
                    $note = new GeneralNote();
                }

                // Update note content and metadata
                $note->content = $request->input('content', '');
                $note->last_edited_by = $user->id;
                $note->last_edited_role = $user->role;
                $note->last_edited_at = now();
                $note->save();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Catatan general berhasil disimpan',
                    'last_edited_by' => $note->last_edited_by,
                    'last_edited_role' => $note->last_edited_role,
                    'last_edited_at' => $note->last_edited_at,
                    'last_editor_name' => $user->name,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error saving general note: ' . $e->getMessage());
                return response()->json(['error' => 'Gagal menyimpan catatan: ' . $e->getMessage()], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unexpected error in GeneralNoteController: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan sistem'], 500);
        }
    }
}
