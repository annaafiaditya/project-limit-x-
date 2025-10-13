<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MikrobiologiSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'role', 'name', 'jabatan', 'tanda_tangan', 'tanggal', 'status'
    ];

    public function form()
    {
        return $this->belongsTo(MikrobiologiForm::class, 'form_id');
    }
}
