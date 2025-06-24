<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMenuPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Panggil fungsi canAccessMenu yang sudah kita buat di model User.
        // Jika fungsi itu mengembalikan false...
        if (!Auth::user()->canAccessMenu($permission)) {
            // ...hentikan permintaan dan tampilkan halaman error 403 (Forbidden).
            abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES UNTUK MENU INI.');
        }

        // Jika user memiliki akses, lanjutkan permintaan ke controller.
        return $next($request);
    }
}
