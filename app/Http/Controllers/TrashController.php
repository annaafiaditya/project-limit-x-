<?php

namespace App\Http\Controllers;

use App\Models\KimiaForm;
use App\Models\MikrobiologiForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type', 'all');
        $perPage = $request->input('perPage', 10);
        
        $kimiaForms = collect();
        $mikrobiologiForms = collect();
        
        if ($type === 'all' || $type === 'kimia') {
            $kimiaQuery = KimiaForm::onlyTrashed();
            
            if ($search) {
                $kimiaQuery->where(function($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                      ->orWhere('no', 'like', "%$search%")
                      ->orWhere('tanggal', 'like', "%$search%");
                });
            }
            
            $kimiaForms = $kimiaQuery->with(['entries', 'signatures'])
                ->orderBy('deleted_at', 'desc')
                ->paginate($perPage, ['*'], 'kimia_page')
                ->appends($request->except('kimia_page'));
        }
        
        if ($type === 'all' || $type === 'mikrobiologi') {
            $mikroQuery = MikrobiologiForm::onlyTrashed();
            
            if ($search) {
                $mikroQuery->where(function($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                      ->orWhere('no', 'like', "%$search%")
                      ->orWhere('tgl_inokulasi', 'like', "%$search%")
                      ->orWhere('tgl_pengamatan', 'like', "%$search%");
                });
            }
            
            $mikrobiologiForms = $mikroQuery->with(['entries', 'signatures'])
                ->orderBy('deleted_at', 'desc')
                ->paginate($perPage, ['*'], 'mikro_page')
                ->appends($request->except('mikro_page'));
        }
        
        return view('trash.index', compact('kimiaForms', 'mikrobiologiForms', 'search', 'type', 'perPage'));
    }
    
    public function restoreKimia($id)
    {
        $kimia_form = KimiaForm::onlyTrashed()->findOrFail($id);
        $kimia_form->restore();
        return redirect()->route('kimia.index')->with([
            'success' => 'Form Kimia berhasil dikembalikan!',
            'highlighted_form' => $kimia_form->id
        ]);
    }
    
    public function restoreMikrobiologi($id)
    {
        $mikrobiologi_form = MikrobiologiForm::onlyTrashed()->findOrFail($id);
        $mikrobiologi_form->restore();
        return redirect()->route('mikrobiologi-forms.index')->with([
            'success' => 'Form Mikrobiologi berhasil dikembalikan!',
            'highlighted_form' => $mikrobiologi_form->id
        ]);
    }
    
    public function forceDeleteKimia($id)
    {
        $kimia_form = KimiaForm::onlyTrashed()->findOrFail($id);
        $kimia_form->forceDelete();
        return redirect()->route('trash.index')->with('success', 'Form Kimia berhasil dihapus permanen!');
    }
    
    public function forceDeleteMikrobiologi($id)
    {
        $mikrobiologi_form = MikrobiologiForm::onlyTrashed()->findOrFail($id);
        $mikrobiologi_form->forceDelete();
        return redirect()->route('trash.index')->with('success', 'Form Mikrobiologi berhasil dihapus permanen!');
    }
}