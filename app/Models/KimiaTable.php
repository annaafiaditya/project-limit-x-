<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KimiaTable extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id', 'name'
    ];

    public function form()
    {
        return $this->belongsTo(KimiaForm::class, 'form_id');
    }

    public function columns()
    {
        return $this->hasMany(KimiaColumn::class, 'table_id');
    }

    public function entries()
    {
        return $this->hasMany(KimiaEntry::class, 'table_id');
    }
}
