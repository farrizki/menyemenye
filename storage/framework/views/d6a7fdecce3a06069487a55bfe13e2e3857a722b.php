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
            <?php echo $__env->make('layouts.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php if(isset($header)): ?>
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <?php echo e($header); ?>

                    </div>
                </header>
            <?php endif; ?>

            <main class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex">
                    
                    <div class="w-1/5 bg-white shadow-sm sm:rounded-lg p-6 mr-4">
                        <h3 class="font-semibold text-lg text-gray-800 mb-4">Menu Aplikasi</h3>
                        <nav>
                            <ul>
                                <?php if(auth()->guard()->check()): ?>
                                    <li class="mb-2">
                                        <a href="<?php echo e(route('dashboard')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Dashboard</a>
                                    </li>

                                    
                                    <?php if(Auth::user()->canAccessMenu('pengurangan.create')): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('pengurangan.create')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Pengurangan SPPT</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user()->canAccessMenu('laporan.pengurangan')): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('laporan.pengurangan')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Laporan Pengurangan</a>
                                        </li>
                                    <?php endif; ?>

                                    
                                    <?php if(Auth::user()->canAccessMenu('denda_administratif.create')): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('denda_administratif.create')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Penghapusan Denda</a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if(Auth::user()->canAccessMenu('denda_administratif.index')): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('denda_administratif.index')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Laporan Denda</a>
                                        </li>
                                    <?php endif; ?>

                                    
                                    <?php if(Auth::user()->isAdmin()): ?>
                                        <li class="mb-2">
                                            <a href="<?php echo e(route('users.index')); ?>" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-md">Manajemen User</a>
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