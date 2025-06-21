<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'password',
        'name',
        'nip',
        'tgl_dibuat',
        'tgl_berlaku',
        'email',
        'role', // PERBAIKAN: Tambahkan 'role'
        'allowed_menus', // PERBAIKAN: Tambahkan 'allowed_menus'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tgl_dibuat' => 'datetime',
        'tgl_berlaku' => 'date',
        'allowed_menus' => 'array', // PERBAIKAN: Cast 'allowed_menus' sebagai array JSON
    ];

    // PERBAIKAN: Method Helper untuk mengecek role
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    // PERBAIKAN: Method Helper untuk mengecek izin akses menu
    public function canAccessMenu(string $routeName): bool
    {
        // Admin bisa mengakses semua menu
        if ($this->isAdmin()) {
            return true;
        }

        // Operator hanya bisa mengakses menu yang ada di allowed_menus
        if ($this->isOperator() && is_array($this->allowed_menus)) {
            return in_array($routeName, $this->allowed_menus);
        }

        return false;
    }
}