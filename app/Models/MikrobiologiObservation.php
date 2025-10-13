<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MikrobiologiObservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'tanggal', 'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function form()
    {
        return $this->belongsTo(MikrobiologiForm::class, 'form_id');
    }
}


