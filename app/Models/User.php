<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'note',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function forms()
    {
        return $this->hasMany(MikrobiologiForm::class, 'created_by');
    }

    // Method untuk cek role
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    // Method untuk cek apakah bisa approve role tertentu
    public function canApprove($targetRole)
    {
        $roleHierarchy = [
            'supervisor' => ['supervisor', 'staff', 'technician'],
            'staff' => ['staff', 'technician'],
            'technician' => ['technician'],
        ];

        return in_array($targetRole, $roleHierarchy[$this->role] ?? []);
    }

    // Method untuk cek apakah user adalah guest
    public function isGuest()
    {
        return $this->role === 'guest';
    }

    // Method untuk cek apakah user bisa melakukan aksi (create, edit, delete)
    public function canPerformActions()
    {
        return !$this->isGuest();
    }

    // Method untuk mendapatkan jabatan berdasarkan role
    public function getJabatanAttribute()
    {
        $jabatan = [
            'supervisor' => 'QA Supervisor',
            'staff' => 'QA Staff',
            'technician' => 'QA Lab. Technician',
            'guest' => 'Guest User',
        ];

        return $jabatan[$this->role] ?? 'Unknown';
    }

}
