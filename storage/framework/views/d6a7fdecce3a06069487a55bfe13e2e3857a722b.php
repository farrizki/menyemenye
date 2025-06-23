<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            
            
            <div class="sticky top-0 z-50 w-full">
                <?php echo $__env->make('layouts.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <?php if(isset($header)): ?>
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <?php echo e($header); ?>

                        </div>
                    </header>
                <?php endif; ?>
            </div>

            <main class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex">
                    
                    <div class="w-1/5 bg-white shadow-sm sm:rounded-lg p-6 mr-4">
                        <h3 class="font-semibold text-lg text-gray-800 mb-4">Menu Aplikasi</h3>
                        <nav>
                            <ul>
                                <?php if(auth()->guard()->check()): ?>
                                    <li class="mb-2">
                                        <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('dashboard')): ?> bg-gray-200 <?php endif; ?>">Dashboard</a>
                                    </li>

                                    
                                    <?php if(Auth::user()->canAccessMenu('pengurangan.create') || Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('pengurangan.create')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('pengurangan.create')): ?> bg-gray-200 <?php endif; ?>">Pengurangan SPPT</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user()->canAccessMenu('laporan.pengurangan') || Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('laporan.pengurangan')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('laporan.pengurangan')): ?> bg-gray-200 <?php endif; ?>">Laporan Pengurangan</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    
                                    <?php if(Auth::user()->canAccessMenu('denda_administratif.create') || Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('denda_administratif.create')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('denda_administratif.create')): ?> bg-gray-200 <?php endif; ?>">Penghapusan Denda</a>
                                        </li>
                                    <?php endif; ?>
                                     <?php if(Auth::user()->canAccessMenu('denda_administratif.index') || Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('denda_administratif.index')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('denda_administratif.index')): ?> bg-gray-200 <?php endif; ?>">Laporan Denda</a>
                                        </li>
                                    <?php endif; ?>

                                    
                                    <?php if(Auth::user()->canAccessMenu('pembatalan.create') || Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('pembatalan.create')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('pembatalan.create')): ?> bg-gray-200 <?php endif; ?>">Pembatalan SPPT</a>
                                        </li>
                                    <?php endif; ?>
                                     <?php if(Auth::user()->canAccessMenu('pembatalan.index') || Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('pembatalan.index')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('pembatalan.index')): ?> bg-gray-200 <?php endif; ?>">Laporan Pembatalan</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user()->canAccessMenu('penggabungan.create') || Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('penggabungan.create')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('penggabungan.create')): ?> bg-gray-200 font-semibold <?php endif; ?>">Penggabungan SPPT</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user()->canAccessMenu('penggabungan.index') || Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('penggabungan.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('penggabungan.index')): ?> bg-gray-200 font-semibold <?php endif; ?>">Laporan Penggabungan</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    
                                    <?php if(Auth::user()->canAccessMenu('dafnom.create') || Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('dafnom.create')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('dafnom.create')): ?> bg-gray-200 <?php endif; ?>">Pembentukan Dafnom</a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    
                                    <?php if(Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('users.index')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md <?php if(request()->routeIs('users.index')): ?> bg-gray-200 <?php endif; ?>">Manajemen User</a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>

                    
                    <div class="w-4/5 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <?php echo e($slot); ?>

                    </div>
                </div>
            </main>

            <?php echo $__env->yieldPushContent('scripts'); ?>
        </div>
    </body>
</html><?php /**PATH D:\xampp\htdocs\aplikasi-final-sppt\resources\views/layouts/app.blade.php ENDPATH**/ ?>