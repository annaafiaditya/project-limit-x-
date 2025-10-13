<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MikrobiologiColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'nama_kolom', 'tipe_kolom', 'urutan'
    ];

    public function form()
    {
        return $this->belongsTo(MikrobiologiForm::class, 'form_id');
    }
}
