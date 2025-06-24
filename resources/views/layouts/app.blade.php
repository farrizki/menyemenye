<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">


        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            
            {{-- PERUBAHAN DI SINI: Wrapper untuk membuat header menjadi sticky --}}
            <div class="sticky top-0 z-50 w-full">
                @include('layouts.navigation')

                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif
            </div>

            <main class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex">
                    {{-- Sidebar Navigasi --}}
                    <div class="w-1/5 bg-white shadow-sm sm:rounded-lg p-6 mr-4">
                        <h3 class="font-semibold text-lg text-gray-800 mb-4">Menu Aplikasi</h3>
                        <nav>
                            <ul>
                                @auth
                                    <li class="mb-2">
                                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('dashboard')) bg-gray-200 @endif">Dashboard</a>
                                    </li>

                                    {{-- Menu Pengurangan --}}
                                    @if (Auth::user()->canAccessMenu('pengurangan.create'))
                                        <li class="mb-2">
                                            <a href="{{ route('pengurangan.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('pengurangan.create')) bg-gray-200 @endif">Pengurangan SPPT</a>
                                        </li>
                                    @endif
                                    @if (Auth::user()->canAccessMenu('laporan.pengurangan'))
                                        <li class="mb-2">
                                            <a href="{{ route('laporan.pengurangan') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('laporan.pengurangan')) bg-gray-200 @endif">Laporan Pengurangan</a>
                                        </li>
                                    @endif
                                    
                                    {{-- Menu Denda --}}
                                    @if (Auth::user()->canAccessMenu('denda_administratif.create'))
                                        <li class="mb-2">
                                            <a href="{{ route('denda_administratif.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('denda_administratif.create')) bg-gray-200 @endif">Penghapusan Denda</a>
                                        </li>
                                    @endif
                                     @if (Auth::user()->canAccessMenu('denda_administratif.index'))
                                        <li class="mb-2">
                                            <a href="{{ route('denda_administratif.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('denda_administratif.index')) bg-gray-200 @endif">Laporan Denda</a>
                                        </li>
                                    @endif

                                    {{-- Menu Pembatalan --}}
                                    @if (Auth::user()->canAccessMenu('pembatalan.create'))
                                        <li class="mb-2">
                                            <a href="{{ route('pembatalan.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('pembatalan.create')) bg-gray-200 @endif">Pembatalan SPPT</a>
                                        </li>
                                    @endif
                                     @if (Auth::user()->canAccessMenu('pembatalan.index'))
                                        <li class="mb-2">
                                            <a href="{{ route('pembatalan.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('pembatalan.index')) bg-gray-200 @endif">Laporan Pembatalan</a>
                                        </li>
                                    @endif
                                    @if (Auth::user()->canAccessMenu('penggabungan.create'))
                                        <li class="mb-2">
                                            <a href="{{ route('penggabungan.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('penggabungan.create')) bg-gray-200 font-semibold @endif">Penggabungan SPPT</a>
                                        </li>
                                    @endif
                                    @if (Auth::user()->canAccessMenu('penggabungan.index'))
                                        <li class="mb-2">
                                            <a href="{{ route('penggabungan.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('penggabungan.index')) bg-gray-200 font-semibold @endif">Laporan Penggabungan</a>
                                        </li>
                                    @endif
                                    
                                    {{-- Menu Dafnom --}}
                                    @if (Auth::user()->canAccessMenu('dafnom.create'))
                                        <li class="mb-2">
                                            <a href="{{ route('dafnom.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('dafnom.create')) bg-gray-200 @endif">Pembentukan Dafnom</a>
                                        </li>
                                    @endif
                                    
                                    {{-- Menu Manajemen User (Hanya Admin) --}}
                                    @if (Auth::user()->isAdmin())
                                        <li class="mb-2">
                                            <a href="{{ route('users.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md @if(request()->routeIs('users.index')) bg-gray-200 @endif">Manajemen User</a>
                                        </li>
                                    @endif
                                @endauth
                            </ul>
                        </nav>
                    </div>

                    {{-- Konten Utama --}}
                    <div class="w-4/5 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        {{ $slot }}
                    </div>
                </div>
            </main>

            @stack('scripts')
        </div>
    </body>
</html>