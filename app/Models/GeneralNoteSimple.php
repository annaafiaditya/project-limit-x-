<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralNoteSimple extends Model
{
    use HasFactory;

    protected $table = 'general_notes_v2';

    protected $fillable = [
        'content',
        'last_edited_by',
        'last_edited_role',
        'last_edited_at',
    ];

    protected $casts = [
        'last_edited_at' => 'datetime',
    ];

    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public static function getLatest()
    {
        return static::orderBy('updated_at', 'desc')->first();
    }
}
