<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pelayanan PBB - BAPENDA Nganjuk</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="antialiased bg-gray-100 dark:bg-gray-900">
    <div class="relative min-h-screen flex flex-col items-center justify-center">
        <!-- Header untuk Login & Register -->
        <div class="absolute top-0 right-0 p-6 text-right">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>
                   
                @endauth
            @endif
        </div>

        <!-- Konten Utama -->
        <main class="text-center">
            <div class="flex items-center justify-center mb-4">
                 <img src="{{ asset('images/Nganjuk.png') }}" alt="Logo BAPENDA" class="h-30 w-20">
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 dark:text-white">
                Sistem Informasi Pelayanan PBB
            </h1>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                Badan Pendapatan Daerah Kabupaten Nganjuk
            </p>
            <div class="mt-8">
                <a href="{{ route('login') }}" class="inline-block bg-blue-600 text-white font-bold py-3 px-8 rounded-lg text-lg hover:bg-blue-700 transition duration-300">
                    Masuk ke Aplikasi
                </a>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="absolute bottom-0 w-full text-center p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} BAPENDA Kabupaten Nganjuk. All rights reserved.
            </p>
        </footer>
    </div>
</body>
</html>
