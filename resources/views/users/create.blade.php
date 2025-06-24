<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah User Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf
                        
                        <div>
                            <x-input-label for="name" :value="__('Nama')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        </div>

                        {{-- === FIELD BARU UNTUK USERNAME === --}}
                        <div class="mt-4">
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="nip" :value="__('NIP')" />
                            <x-text-input id="nip" class="block mt-1 w-full" type="text" name="nip" :value="old('nip')" required />
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select name="role" id="role" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="operator" @if(old('role') == 'operator') selected @endif>Operator</option>
                                <option value="admin" @if(old('role') == 'admin') selected @endif>Admin</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="tgl_berlaku" :value="__('Tanggal Berlaku (Opsional)')" />
                            <x-text-input id="tgl_berlaku" class="block mt-1 w-full" type="date" name="tgl_berlaku" :value="old('tgl_berlaku')" />
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                        </div>

                        <!-- === BAGIAN IZIN AKSES === -->
                        <div id="permissions-section" class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900">Izin Akses Menu</h3>
                            <p class="text-sm text-gray-600 mb-4">Pilih menu mana saja yang dapat diakses oleh user ini. Admin akan selalu memiliki akses penuh.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 border p-4 rounded-md">
                                @foreach ($permissions as $permission => $label)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Simpan User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
