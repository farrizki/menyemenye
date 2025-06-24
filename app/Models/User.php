<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'nip',
        'role',
        'permissions',
        'tgl_berlaku'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
        'tgl_berlaku' => 'date',
    ];
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Memeriksa hak akses menu dengan lebih aman.
     */
    public function canAccessMenu($permission)
    {
        // Admin selalu bisa akses semua menu.
        if ($this->isAdmin()) {
            return true;
        }

        // Untuk Operator, lakukan pemeriksaan lebih lanjut.
        if ($this->role === 'operator') {
            // 1. Tolak akses jika akun sudah kadaluarsa (jika tanggal berlaku diatur).
            if ($this->tgl_berlaku && $this->tgl_berlaku->isPast()) {
                return false;
            }

            // 2. Pastikan 'permissions' adalah sebuah array sebelum diperiksa.
            $userPermissions = $this->permissions ?? [];
            if (!is_array($userPermissions)) {
                return false; // Menjaga jika data di database tidak valid.
            }

            // 3. Kembalikan true hanya jika izin ditemukan di dalam array.
            return in_array($permission, $userPermissions);
        }

        // Secara default, tolak akses jika peran tidak dikenali.
        return false;
    }
}
