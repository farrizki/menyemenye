<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Pembatalan SPPT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h3 class="text-xl font-bold mb-4">Riwayat Pembatalan SPPT</h3>

                    <div class="mb-4 flex items-center space-x-2">
                        <x-text-input type="text" id="searchInput" placeholder="Cari NOP/Nama/Tahun/SK..." class="w-full" value="{{ request('search') }}" />
                        
                        {{-- PENAMBAHAN TOMBOL CETAK SEMUA --}}
                        <a href="{{ route('pembatalan.filter-cetak-pdf') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 whitespace-nowrap">Cetak Semua</a>

                        <a href="{{ route('pembatalan.index') }}" id="resetButton" class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 {{ request('search') ? '' : 'hidden' }}">Reset</a>
                    </div>

                    <div id="laporanTableContainer">
                         @include('pembatalan.partials.laporan_table', ['laporan' => $laporan])
                    </div>

                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        {{-- Script AJAX tetap sama --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('searchInput');
                const resetButton = document.getElementById('resetButton');
                const container = document.getElementById('laporanTableContainer');
                let searchTimeout;

                function fetchLaporan(page = 1) {
                    const query = searchInput.value;
                    const url = new URL("{{ route('pembatalan.index') }}");
                    url.searchParams.set('search', query);
                    url.searchParams.set('page', page);
                    
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        resetButton.classList.toggle('hidden', !query);
                        attachPaginationListeners();
                    })
                    .catch(error => console.error('Error fetching report:', error));
                }

                searchInput.addEventListener('input', () => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => fetchLaporan(1), 300);
                });

                function attachPaginationListeners() {
                    container.querySelectorAll('.pagination a').forEach(link => {
                        link.addEventListener('click', function (e) {
                            e.preventDefault();
                            const page = new URL(this.href).searchParams.get('page');
                            fetchLaporan(page);
                        });
                    });
                }
                
                attachPaginationListeners();
            });
        </script>
    @endpush
</x-app-layout>