<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Pengurangan SPPT') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Hasil Proses Pengurangan Terakhir</h3>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (!empty($flashResults))
                        <div class="mb-4">
                            <h4 class="mb-2">Detail Proses:</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOP</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($flashResults as $result)
                                            <tr class="{{ $result['status'] == 'Berhasil' ? 'bg-green-50' : ($result['status'] == 'Gagal' || $result['status'] == 'Error' ? 'bg-red-50' : 'bg-yellow-50') }}">
                                                <td>
                                                    {{ $result['formatted_nop'] ?? $result['nop'] ?? '-' }}
                                                </td>
                                                <td>{{ $result['status'] }}</td>
                                                <td>{{ $result['message'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end mb-4">
                        {{-- PERBAIKAN PENTING: Arahkan ke route filter PDF --}}
                        <a href="{{ route('laporan.pengurangan.filter-pdf') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150" target="_blank">
                            {{ __('Cetak Semua PDF (Filter)') }}
                        </a>
                    </div>

                    <h3 class="text-xl font-bold mb-4 mt-5">Riwayat Pengurangan SPPT</h3>

                    <div class="mb-4 flex items-center space-x-2">
                        <x-text-input type="text" id="searchInput" placeholder="Cari NOP/Nama/Tahun/Alamat/SK/Jenis..." class="w-full" value="{{ request('search') }}" />
                        <a href="{{ route('laporan.pengurangan') }}" id="resetButton" class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 {{ request('search') ? '' : 'hidden' }}">Reset</a>
                    </div>

                    <div id="laporanTableContainer">
                        @include('pengurangan.partials.laporan_table', ['laporanPengurangan' => $laporanPengurangan])
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const searchInput = document.getElementById('searchInput');
            const resetButton = document.getElementById('resetButton');
            const laporanTableContainer = document.getElementById('laporanTableContainer');
            let searchTimeout;

            function fetchLaporan(searchQuery = '', page = 1) {
                clearTimeout(searchTimeout);

                searchTimeout = setTimeout(() => {
                    const url = new URL("{{ route('laporan.pengurangan') }}");
                    url.searchParams.set('search', searchQuery);
                    url.searchParams.set('page', page);
                    url.searchParams.set('ajax', 'true');

                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        laporanTableContainer.innerHTML = html;

                        if (searchQuery) {
                            resetButton.classList.remove('hidden');
                        } else {
                            resetButton.classList.add('hidden');
                        }

                        attachPaginationListeners();
                    })
                    .catch(error => console.error('Error fetching laporan:', error));
                }, 300);
            }

            searchInput.addEventListener('input', (event) => {
                fetchLaporan(event.target.value);
            });

            resetButton.addEventListener('click', (event) => {
                event.preventDefault();
                searchInput.value = '';
                fetchLaporan('');
            });

            function attachPaginationListeners() {
                laporanTableContainer.querySelectorAll('.pagination a').forEach(link => {
                    link.removeEventListener('click', handlePaginationClick);
                    link.addEventListener('click', handlePaginationClick);
                });
            }

            function handlePaginationClick(event) {
                event.preventDefault();
                const url = new URL(event.target.href);
                const page = url.searchParams.get('page');
                fetchLaporan(searchInput.value, page);
            }

            document.addEventListener('DOMContentLoaded', attachPaginationListeners);
        </script>
    @endpush
</x-app-layout>