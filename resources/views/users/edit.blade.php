<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Edit User: {{ $user->name }}</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username', $user->username)" required />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email (Opsional)')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Password (Isi jika ingin mengubah)')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="nip" :value="__('NIP (Opsional)')" />
                            <x-text-input id="nip" class="block mt-1 w-full" type="text" name="nip" :value="old('nip', $user->nip)" />
                            <x-input-error :messages="$errors->get('nip')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="tgl_berlaku" :value="__('Tanggal Berlaku (Opsional)')" />
                            <x-text-input id="tgl_berlaku" class="block mt-1 w-full" type="date" name="tgl_berlaku" :value="old('tgl_berlaku', $user->tgl_berlaku ? \Carbon\Carbon::parse($user->tgl_berlaku)->format('Y-m-d') : '')" />
                            <x-input-error :messages="$errors->get('tgl_berlaku')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="toggleAllowedMenus()">
                                <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>Operator</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div class="mb-4" id="allowed_menus_section" style="display: {{ old('role', $user->role) == 'operator' ? 'block' : 'none' }}">
                            <x-input-label for="allowed_menus" :value="__('Izinkan Akses Menu (untuk Operator)')" class="mb-2" />
                            @foreach($allMenus as $routeName => $menuLabel)
                                <div class="flex items-center mb-1">
                                    <input type="checkbox" id="menu_{{ $routeName }}" name="allowed_menus[]" value="{{ $routeName }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ (is_array(old('allowed_menus')) && in_array($routeName, old('allowed_menus'))) || (!is_null($user->allowed_menus) && in_array($routeName, $user->allowed_menus) && old('allowed_menus') === null) ? 'checked' : '' }}>
                                    <label for="menu_{{ $routeName }}" class="ms-2 text-sm text-gray-600">{{ $menuLabel }}</label>
                                </div>
                            @endforeach
                            <x-input-error :messages="$errors->get('allowed_menus')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function toggleAllowedMenus() {
                const roleSelect = document.getElementById('role');
                const allowedMenusSection = document.getElementById('allowed_menus_section');
                if (roleSelect.value === 'operator') {
                    allowedMenusSection.style.display = 'block';
                } else {
                    allowedMenusSection.style.display = 'none';
                    // Opsional: Uncheck semua checkbox jika role diubah ke admin
                    allowedMenusSection.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
            }
            document.addEventListener('DOMContentLoaded', toggleAllowedMenus);
        </script>
    @endpush
</x-app-layout>