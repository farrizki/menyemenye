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
                    <p class="mb-4 text-sm text-gray-600">Berikut adalah status NOP yang akan diproses untuk denda:</p>

                    <div class="overflow-x-auto border rounded-lg mb-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOP</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama WP</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat WP</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Letak OP</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pokok</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jml Pajak</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sanksi</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harus Dibayar</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pesan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- ✅ PERBAIKAN: Logika rumit dihapus, semua data ditampilkan --}}
                                @forelse ($dataToProcess as $data)
                                    @php
                                        $currentStatus = $data['status_validasi'] ?? 'N/A';
                                    @endphp
                                    <tr class="
                                        @if($currentStatus == 'Siap Diproses') bg-blue-50 
                                        @elseif($currentStatus == 'Gagal' || $currentStatus == 'Error') bg-red-50 
                                        @else bg-yellow-50 @endif
                                    ">
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data['formatted_nop'] ?? $data['nop'] ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data['thn_pajak_sppt'] ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data['nm_wp_sppt'] ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data['alamat_wp'] ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">{{ $data['letak_op'] ?? '-' }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right">Rp {{ number_format($data['pokok'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right">Rp {{ number_format($data['denda'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right">Rp {{ number_format($data['jumlah_pajak'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right">Rp {{ number_format($data['sanksi_administratif'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-right font-semibold">Rp {{ number_format($data['yang_harus_dibayar'] ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($currentStatus == 'Siap Diproses') bg-blue-100 text-blue-800 
                                                @elseif($currentStatus == 'Gagal' || $currentStatus == 'Error') bg-red-100 text-red-800 
                                                @else bg-yellow-100 text-yellow-800 @endif
                                            ">
                                                {{ $currentStatus }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600">{{ $data['message'] ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center py-4 px-6 text-gray-500">
                                            Tidak ada data untuk dipratinjau.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @php
                        $jumlahNopSiapDiproses = collect($dataToProcess)->where('status_validasi', 'Siap Diproses')->count();
                    @endphp

                    <div class="mt-4 border-t pt-4">
                        <p class="mb-2 font-semibold text-gray-800">Jumlah NOP yang akan diproses: <strong>{{ $jumlahNopSiapDiproses }}</strong></p>
                        <p>Tanggal Jatuh Tempo Baru akan diubah menjadi: <strong>{{ $tglJatuhTempoBaru ? \Carbon\Carbon::parse($tglJatuhTempoBaru)->format('d-m-Y') : '-' }}</strong></p>
                        <p>Nomor SK: <strong>{{ $noSkLengkap ?? '-' }}</strong></p>
                        <p class="mb-4">Tanggal SK: <strong>{{ $tglSk ? \Carbon\Carbon::parse($tglSk)->format('d-m-Y') : '-' }}</strong></p>
                    </div>

                    <div class="flex justify-end mt-4">
                        <a href="{{ route('denda_administratif.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 mr-2">
                            {{ __('Batal') }}
                        </a>
                        @if ($jumlahNopSiapDiproses > 0)
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