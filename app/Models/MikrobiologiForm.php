<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MikrobiologiForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'no', 'no_dokumen', 'tgl_inokulasi', 'tgl_pengamatan', 'created_by'
    ];

    public function entries()
    {
        return $this->hasMany(MikrobiologiEntry::class, 'form_id');
    }

    public function signatures()
    {
        return $this->hasMany(MikrobiologiSignature::class, 'form_id');
    }

    public function columns()
    {
        return $this->hasMany(MikrobiologiColumn::class, 'form_id')->orderBy('urutan');
    }

    public function observations()
    {
        return $this->hasMany(MikrobiologiObservation::class, 'form_id')->orderBy('tanggal');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
