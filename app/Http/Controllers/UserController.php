<?php

namespace App\Http\Controllers;

use App\Models\User; // PERBAIKAN: Impor Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon; // Untuk tgl_dibuat dan tgl_berlaku

class UserController extends Controller
{
    /**
     * Menampilkan daftar user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat user baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Daftar menu yang tersedia untuk operator
        $allMenus = [
            'dashboard' => 'Dashboard',
            'pengurangan.create' => 'Pengurangan SPPT',
            'laporan.pengurangan' => 'Laporan Pengurangan',
            // Tambahkan nama route lain yang ingin diizinkan untuk operator
        ];
        return view('users.create', compact('allMenus'));
    }

    /**
     * Menyimpan user baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nip' => ['nullable', 'string', 'max:255'],
            'tgl_berlaku' => ['nullable', 'date'],
            'role' => ['required', 'string', 'in:admin,operator'], // Validasi role
            'allowed_menus' => ['nullable', 'array'], // Validasi array menu
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nip' => $request->nip,
            'tgl_dibuat' => Carbon::now(), // Mengisi tgl_dibuat secara otomatis
            'tgl_berlaku' => $request->tgl_berlaku,
            'role' => $request->role,
            // Simpan allowed_menus hanya jika role adalah operator
            'allowed_menus' => $request->role === 'operator' ? $request->allowed_menus : null,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $allMenus = [
            'dashboard' => 'Dashboard',
            'pengurangan.create' => 'Pengurangan SPPT',
            'laporan.pengurangan' => 'Laporan Pengurangan',
            // Tambahkan nama route lain yang ingin diizinkan untuk operator
        ];
        return view('users.edit', compact('user', 'allMenus'));
    }

    /**
     * Mengupdate user di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->id], // Unique except current user
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id], // Unique except current user
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Password bisa kosong jika tidak diubah
            'nip' => ['nullable', 'string', 'max:255'],
            'tgl_berlaku' => ['nullable', 'date'],
            'role' => ['required', 'string', 'in:admin,operator'],
            'allowed_menus' => ['nullable', 'array'],
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        if ($request->filled('password')) { // Update password hanya jika diisi
            $user->password = Hash::make($request->password);
        }
        $user->nip = $request->nip;
        $user->tgl_berlaku = $request->tgl_berlaku;
        $user->role = $request->role;
        // Simpan allowed_menus hanya jika role adalah operator
        $user->allowed_menus = $request->role === 'operator' ? $request->allowed_menus : null;

        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui!');
    }

    /**
     * Menghapus user dari database.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Pastikan admin tidak bisa menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}