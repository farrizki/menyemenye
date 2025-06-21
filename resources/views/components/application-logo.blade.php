{{-- resources/views/components/application-logo.blade.php --}}

<img src="{{ asset('images/nganjuk.png') }}" alt="{{ config('app.name', 'Logo') }}" class="w-10 h-15 object-contain">
{{-- PERBAIKAN: Tambahkan class object-contain atau object-cover --}}

{{-- Penjelasan:
    - object-contain: Gambar akan diskalakan untuk muat di dalam elemen,
      mempertahankan rasio aspek dan mengisi ruang yang tersedia.
      Bisa ada ruang kosong (letterboxing) di samping/atas-bawah jika rasio tidak cocok.
    - object-cover: Gambar akan diskalakan untuk mengisi seluruh elemen,
      mempertahankan rasio aspek tetapi mungkin memotong bagian gambar.
    - Pilih salah satu yang paling sesuai dengan logomu.
    - Pastikan juga w-20 h-20 sesuai dengan ukuran yang kamu inginkan,
      atau atur hanya w-auto atau h-auto jika ingin bebas dari distorsi.
--}}