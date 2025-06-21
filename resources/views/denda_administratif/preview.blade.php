<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pratinjau Penghapusan Denda Administratif') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Konfirmasi Penghapusan Denda Administratif</h3>

                    <p class="mb-4">Berikut adalah status NOP yang akan diproses untuk denda:</p>

                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun Pajak</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama WP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat WP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Letak OP</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pokok</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pajak</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sanksi Administratif</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yang Harus Dibayar</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pratinjau</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $displayCount = 0; // Hitung berapa baris yang ditampilkan
                                @endphp
                                @foreach ($dataToProcess as $data)
                                    @php
                                        $currentStatus = $data['status_validasi'] ?? 'N/A';
                                        // PERBAIKAN PENTING: Hanya tampilkan Siap Diproses, Gagal, dan Error
                                        // Status 'Tidak Diproses' (karena sudah dibayar) akan diabaikan
                                    @endphp
                                    @if ($currentStatus === 'Siap Diproses' || $currentStatus === 'Gagal' || $currentStatus === 'Error')
                                        @php $displayCount++; @endphp
                                        <tr class="{{ $currentStatus == 'Siap Diproses' ? 'bg-blue-50' : (($currentStatus == 'Gagal' || $currentStatus == 'Error') ? 'bg-red-50' : 'bg-yellow-50') }}">
                                            <td>
                                                {{ $data['formatted_nop'] ?? $data['nop'] ?? '-' }}
                                            </td>
                                            <td>{{ $data['thn_pajak_sppt'] ?? '-' }}</td>
                                            <td>{{ $data['nm_wp_sppt'] ?? '-' }}</td>
                                            <td>{{ $data['alamat_wp'] ?? '-' }}</td>
                                            <td>{{ $data['letak_op'] ?? '-' }}</td>
                                            <td>{{ number_format($data['pokok'] ?? 0, 2, ',', '.') }}</td>
                                            <td>{{ number_format($data['denda'] ?? 0, 2, ',', '.') }}</td>
                                            <td>{{ number_format($data['jumlah_pajak'] ?? 0, 2, ',', '.') }}</td>
                                            <td>{{ number_format($data['sanksi_administratif'] ?? 0, 2, ',', '.') }}</td>
                                            <td>{{ number_format($data['yang_harus_dibayar'] ?? 0, 2, ',', '.') }}</td>
                                            <td>{{ $data['status_validasi'] ?? 'N/A' }}</td>
                                            <td>{{ $data['message'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                {{-- Tambahkan baris empty jika tidak ada data yang ditampilkan --}}
                                @if($displayCount === 0)
                                    <tr>
                                        <td colspan="12" class="text-center py-4 text-gray-500">Tidak ada data yang siap diproses, gagal, atau error untuk ditampilkan.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <p class="mb-2">Tanggal Jatuh Tempo Baru: <strong>{{ $tglJatuhTempoBaru ? \Carbon\Carbon::parse($tglJatuhTempoBaru)->format('d-m-Y') : '-' }}</strong></p>
                    <p class="mb-2">Nomor SK: <strong>{{ $noSkLengkap ?? '-' }}</strong></p>
                    <p class="mb-4">Tanggal SK: <strong>{{ $tglSk ? \Carbon\Carbon::parse($tglSk)->format('d-m-Y') : '-' }}</strong></p>

                    <div class="flex justify-end mt-4">
                        <a href="{{ route('denda_administratif.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                            {{ __('Batalkan') }}
                        </a>
                        {{-- Tombol Konfirmasi & Simpan hanya aktif jika ada data yang Siap Diproses --}}
                        @if (collect($dataToProcess)->contains(fn($item) => ($item['status_validasi'] ?? 'N/A') === 'Siap Diproses'))
                            <form action="{{ route('denda_administratif.confirm') }}" method="POST">
                                @csrf
                                <x-primary-button>
                                    {{ __('Konfirmasi & Simpan') }}
                                </x-primary-button>
                            </form>
                        @else
                            <x-primary-button disabled title="Tidak ada data yang siap disimpan">
                                {{ __('Konfirmasi & Simpan') }}
                            </x-primary-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>