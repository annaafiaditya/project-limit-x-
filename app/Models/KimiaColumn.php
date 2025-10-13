<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KimiaColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'table_id', 'nama_kolom', 'tipe_kolom', 'urutan'
    ];

    public function form()
    {
        return $this->belongsTo(KimiaForm::class, 'form_id');
    }

    public function table()
    {
        return $this->belongsTo(KimiaTable::class, 'table_id');
    }
}
