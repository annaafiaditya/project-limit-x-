<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'last_edited_by',
        'last_edited_role',
        'last_edited_at',
    ];

    protected $casts = [
        'last_edited_at' => 'datetime',
    ];

    /**
     * Get the user who last edited the note
     */
    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    /**
     * Get the latest general note
     */
    public static function getLatest()
    {
        return static::orderBy('updated_at', 'desc')->first();
    }
}
