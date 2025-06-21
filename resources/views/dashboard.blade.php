<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-lg">
                        <span id="currentDate"></span> - <span id="currentTime"></span>
                    </p>
                    <div class="mt-8 border-t pt-8">
                        <p class="text-gray-600">Ini adalah area kerja utama Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function updateDateTime() {
                const now = new Date();
                const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
                document.getElementById('currentDate').textContent = now.toLocaleDateString('id-ID', optionsDate);
                document.getElementById('currentTime').textContent = now.toLocaleTimeString('id-ID', optionsTime);
            }
            updateDateTime();
            setInterval(updateDateTime, 1000);
        </script>
    @endpush
</x-app-layout>