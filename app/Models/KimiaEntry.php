<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KimiaEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'table_id', 'data'
    ];

    protected $casts = [
        'data' => 'array',
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
