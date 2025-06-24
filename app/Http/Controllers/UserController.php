<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $allPermissions = [
        'pengurangan.create' => 'Pengurangan SPPT',
        'laporan.pengurangan' => 'Laporan Pengurangan',
        'denda_administratif.create' => 'Penghapusan Denda',
        'denda_administratif.index' => 'Laporan Denda',
        'pembatalan.create' => 'Pembatalan SPPT',
        'pembatalan.index' => 'Laporan Pembatalan',
        'penggabungan.create' => 'Penggabungan SPPT',
        'penggabungan.index' => 'Laporan Penggabungan',
        'dafnom.create' => 'Pembentukan Dafnom',
    ];

    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create')->with('permissions', $this->allPermissions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'nip' => ['required', 'string', 'max:20', 'unique:users'],
            'role' => ['required', 'in:admin,operator'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'permissions' => ['nullable', 'array'],
            'tgl_berlaku' => ['nullable', 'date'], // DIGANTI
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'nip' => $request->nip,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'permissions' => $request->input('permissions', []),
            'tgl_berlaku' => $request->tgl_berlaku, // DIGANTI
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'permissions' => $this->allPermissions
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'nip' => ['required', 'string', 'max:20', 'unique:users,nip,'.$user->id],
            'role' => ['required', 'in:admin,operator'],
            'permissions' => ['nullable', 'array'],
            'tgl_berlaku' => ['nullable', 'date'], // DIGANTI
        ]);

        $dataToUpdate = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'nip' => $request->nip,
            'role' => $request->role,
            'permissions' => $request->input('permissions', []),
            'tgl_berlaku' => $request->tgl_berlaku, // DIGANTI
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => ['required', 'confirmed', Rules\Password::defaults()]]);
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->withErrors('Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
