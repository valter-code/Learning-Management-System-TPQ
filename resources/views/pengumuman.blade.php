<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} [- Pendidikan Al-Qur'an Berkualitas</title>
    <link rel="icon" href="https://placehold.co/32x32/10B981/FFFFFF?text=TPQ" type="image/png">
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* .section-divider {
            border-top: 2px solid #e5e7eb; /* Warna abu-abu muda, bisa diganti ke hitam jika preferensi */
            /* margin-top: 2rem; 
            margin-bottom: 2rem; Sesuaikan jarak bawah
        } */ 
        
        .section-divider-dark {
            border-top: 1px solid #000000;
            margin-top: 3rem;
            margin-bottom: 3rem;
        }
    </style>
</head>
<body class="bg-gray-50 antialiased"> 

    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50"> 
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
    <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
        <img src="https://placehold.co/40x40/0D9488/FFFFFF?text=TPQ" class="h-8 rounded-md" alt="LMS TPQ Logo" />
        <span class="self-center text-2xl font-semibold whitespace-nowrap text-teal-700">{{ config('app.name') }}</span>
    </a>
    <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
        <a href="{{ route('registrasi.santri.create') }}" class="inline-block text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-4 py-2 text-center transition duration-150 ease-in-out">
        Daftar Sekarang!
        </a>
        <button data-collapse-toggle="navbar-cta" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-600 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="navbar-cta" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
            </svg>
        </button>
    </div>
    <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-cta">
        <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white">
        <li>
            <a href="{{ url('/') }}" class="block py-2 px-3 md:p-0 text-gray-700 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Beranda</a>
        </li>
        <li>
            <a href="pengumuman" class="block py-2 px-3 md:p-0 text-teal-600 rounded-sm md:bg-transparent" aria-current="page">Pengumuman</a>
        </li>
        <li>
            <a href="galeri" class="block py-2 px-3 md:p-0 text-gray-700 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Galeri</a>
        </li>
        <li>
            <a href="tentang" class="block py-2 px-3 md:p-0 text-gray-700 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Tentang</a>
        </li>
        <li>
            <a href="kontak" class="block py-2 px-3 md:p-0 text-gray-700 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Hubungi Kami</a>
        </li>
        </ul>
    </div>
    </div>
    </nav>

    <!-- <main>
        <section class="bg-gradient-to-br from-teal-50 via-emerald-50 to-white py-20 px-4 text-center">
            <div class="max-w-screen-md mx-auto">
                <h1 class="mb-5 text-4xl font-extrabold leading-tight tracking-tight text-teal-800 md:text-5xl lg:text-6xl">
                    Selamat Datang di LMS TPQ Lorem Ipsum
                </h1>
                <p class="mb-10 text-lg font-normal text-gray-700 lg:text-xl">
                    TPQ Lorem Ipsum Tempat terbaik untuk belajar karena dibimbing oleh Imam Dolor Sit Amet untuk membaca, memahami, dan mengamalkan Al-Qur'an dengan metode yang efektif dan menyenangkan.
                </p>
                <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0 sm:space-x-4">
                    <a href="#tentang" class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-center
                        text-white bg-teal-600 rounded-lg border border-teal-600
                        hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300
                        transition duration-150 ease-in-out">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
                </div>
        </section>

        <div class="section-divider-black max-w-screen-xl mx-auto px-4"></div> 

        <main> -->
    {{-- SECTION HERO --}}

    {{-- SECTION HEADER GALERI BARU --}}
<section id="galeri-header" class="bg-teal-50 py-10 px-4"> 
    <div class="max-w-screen-xl mx-auto">
        <div class="md:flex md:items-center md:justify-between">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-teal-800 sm:text-4xl">Pengumuman</h2>
                <p class="mt-3 text-lg text-gray-700 max-w-3xl">
                    Pengumuman terbaru yang diumumkan oleh Pengajar TPQ Lorem Ipsum
                </p>
            </div>
            {{-- Breadcrumb atau navigasi singkat (opsional, seperti di contoh merah) --}}
            <div class="mt-4 md:mt-0">
                <nav aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm font-medium text-gray-600">
                        <li><a href="/" class="hover:text-teal-600">Beranda</a></li>
                        <li>
                            <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        </li>
                        <li><span class="text-teal-700" aria-current="page">Pengumuman</span></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

    {{-- SECTION PENGUMUMAN BARU --}}
    <section id="pengumuman-content" class="py-16 bg-white px-4">
    <div class="max-w-screen-lg mx-auto">
        <div class="text-center mb-8">
            <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-teal-800 lg:text-4xl">Pengumuman Terbaru</h2>
            <p class="text-lg text-gray-600">Informasi dan berita terkini dari {{ config('app.name') }}.</p>
        </div>

        {{-- Search Bar Section --}}
        <div class="mb-8 md:mb-10">
            {{-- Pastikan action merujuk ke rute yang benar (pengumuman.index) --}}
            <form action="{{ route('pengumuman.index') }}" method="GET" class="max-w-lg mx-auto md:max-w-xl">
                {{-- @csrf --}} {{-- Tidak wajib untuk GET, tapi tidak masalah jika ada --}}
                <label for="search-pengumuman" class="mb-2 text-sm font-medium text-gray-900 sr-only">Cari Pengumuman</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    {{-- Gunakan variabel $keyword yang dikirim dari controller --}}
                    <input type="search" id="search-pengumuman" name="keyword" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-teal-500 focus:border-teal-500" placeholder="Ketik kata kunci pengumuman..." value="{{ $keyword ?? '' }}" />
                    <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-4 py-2 transition duration-150 ease-in-out">
                        Cari
                    </button>
                </div>
            </form>
        </div>
        {{-- End Search Bar Section --}}

        {{-- Kondisi jika tidak ada pengumuman --}}
        @if($pengumumans->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-200">Belum ada pengumuman</h3>
                @if(isset($keyword) && !empty($keyword))
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada pengumuman yang cocok dengan kata kunci "<span class="font-semibold">{{ $keyword }}</span>". Coba kata kunci lain.</p>
                @else
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Silakan cek kembali nanti.</p>
                @endif
            </div>
        @else
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($pengumumans as $item) {{-- Variabel loop adalah $item --}}
                <article class="p-6 bg-gray-50 rounded-lg border border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col">
                    <div class="mb-3">
                    @if($item->foto) {{-- Gunakan $item->foto --}}
                        {{-- Gunakan helper Storage::url() untuk path foto yang benar --}}
                        <img src="{{ Storage::url($item->foto) }}" alt="Foto {{ $item->judul }}" class="rounded-md w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded-md">
                            <span class="text-gray-500">Tidak ada foto</span>
                        </div>
                    @endif
                    </div>
                    <h3 class="mb-2 text-xl font-semibold text-teal-700 hover:text-teal-800">
                        {{-- Link ke halaman detail pengumuman --}}
                        <a href="{{ route('pengumuman.show', $item->slug ?? $item->id) }}">{{ $item->judul }}</a> {{-- Gunakan $item->judul --}}
                    </h3>
                    <p class="text-sm text-gray-500 mb-3">
                        {{-- Gunakan published_at dan format dengan Carbon, dari $item --}}
                        <time datetime="{{ $item->published_at ? $item->published_at->toIso8601String() : '' }}">
                            {{ $item->published_at ? $item->published_at->translatedFormat('d F Y, H:i') : ($item->created_at ? $item->created_at->translatedFormat('d F Y, H:i') : 'Tanggal tidak tersedia') }}
                        </time>
                        {{-- Akses user dari $item --}}
                        @if($item->user)
                         - {{ $item->user->name }}
                        @endif
                    </p>
                    <div class="text-gray-700 mb-4 flex-grow prose prose-sm max-w-none line-clamp-4"> {{-- Tambahkan prose untuk styling konten HTML --}}
                        {!! \Illuminate\Support\Str::limit(strip_tags($item->konten), 120) !!} {{-- Gunakan $item->konten --}}
                    </div>
                    {{-- Link ke halaman detail pengumuman --}}
                    <a href="{{ route('pengumuman.show', $item->slug ?? $item->id) }}" class="inline-flex items-center font-medium text-teal-600 hover:text-teal-700 hover:underline mt-auto">
                        Baca Selengkapnya
                        <svg class="w-4 h-4 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                        </svg>
                    </a>
                </article>
                @endforeach
            </div>

            @if(!$showAll && $pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-12">
                {{ $pengumumans->appends(request()->query())->links() }} {{-- request()->query() akan membawa semua parameter query string saat ini --}}
            </div>
            @endif
        @endif

        {{-- Tombol Lihat Semua Pengumuman (Opsional) --}}
        @if(!$showAll && $pengumumans instanceof \Illuminate\Pagination\LengthAwarePaginator && $pengumumans->hasMorePages())
            <div class="text-center mt-12">
                {{-- Tambahkan parameter show_all=1 ke URL --}}
                <a href="{{ route('pengumuman.index', array_merge(request()->query(), ['show_all' => 1])) }}" class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-6 py-3 transition duration-150 ease-in-out">
                    Lihat Semua Pengumuman
                </a>
            </div>
            @endif
    </div>
</section>

{{-- @endsection --}}

    <div class="section-divider max-w-screen-xl mx-auto px-4"></div> 

</main>
        
        <div class="section-divider-black max-w-screen-xl mx-auto px-4"></div> 

       

        <!-- <div class="section-divider max-w-screen-xl mx-auto px-4"></div>  -->
    </main>

    <footer id="kontak" class="bg-teal-700 text-white"> 
        <div class="mx-auto w-full max-w-screen-xl p-4 py-10 lg:py-12"> 
            <div class="md:flex md:justify-between">
            <div class="mb-8 md:mb-0"> 
                <a href="#" class="flex items-center">
                    <img src="https://placehold.co/40x40/FFFFFF/0D9488?text=TPQ" class="h-8 me-3 rounded-md" alt="LMS TPQ Logo" /> 
                    <span class="self-center text-2xl font-semibold whitespace-nowrap">{{ config('app.name') }}</span>
                </a>
                <p class="mt-4 text-sm text-white max-w-xs"> 
                 <strong>Alamat: </strong>{{ $settings['contact_address'] ?? '-' }} <br>
                    <a href="tel:{{ $settings['contact_phone'] ?? '-' }}" class="hover:underline text-white hover:text-teal-50"><strong>Telp: </strong>{{ $settings['contact_phone'] ?? '-' }}</a><br>
                    <a href="mailto:{{ $settings['contact_email'] ?? '-' }}" class="hover:underline text-white hover:text-teal-50"><strong>Email:</strong> {{ $settings['contact_email'] ?? '-' }}</a>
                </p>
            </div>
            <div class="grid grid-cols-2 gap-8 sm:gap-10 sm:grid-cols-3"> 
                <div>
                    <h2 class="mb-6 text-sm font-semibold uppercase text-white">Navigasi</h2>
                    <ul class="text-teal-100 font-medium">
                        <li class="mb-4">
                            <a href="/" class="text-white hover:underline hover:text-white">Beranda</a>
                        </li>
                        <li class="mb-4">
                            <a href="#tentang" class="text-white hover:underline hover:text-white">Tentang Kami</a>
                        </li>
                        <li class="mb-4">
                            <a href="#testimoni" class="text-white hover:underline hover:text-white">Testimoni</a>
                        </li>
                        <li>
                            <a href="#" class="text-white hover:underline hover:text-white">Program Belajar</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="mb-6 text-sm font-semibold uppercase text-white">Media Sosial</h2>
                    <ul class="text-teal-100 font-medium">
                        <li class="mb-4">
                            <a href="#" class="text-white hover:underline hover:text-white">Instagram</a>
                        </li>
                        <li class="mb-4">
                            <a href="#" class="text-white hover:underline hover:text-white">Facebook</a>
                        </li>
                         <li>
                            <a href="#" class="text-white hover:underline hover:text-white">Youtube</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="mb-6 text-sm font-semibold uppercase text-white">Informasi</h2>
                    <ul class="text-teal-100 font-medium">
                        <li class="mb-4">
                            <a href="#" class="text-white hover:underline hover:text-white">Kebijakan Privasi</a>
                        </li>
                        <li>
                            <a href="#" class="text-white hover:underline hover:text-white">Syarat & Ketentuan</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <hr class="my-6 border-teal-600 sm:mx-auto lg:my-8" /> 
        <div class="sm:flex sm:items-center sm:justify-between pb-8 px-4"> 
        <span class="text-sm text-gray-200 sm:text-center dark:text-gray-300">© {{ date('Y') }} <a href="#" class="hover:underline hover:text-white">{{-- config('app.name') --}}{{ config('app.name') }}™</a>. Hak Cipta Dilindungi
        </span>
            <div class="flex mt-4 sm:justify-center sm:mt-0 space-x-5"> 
                 <a href="#" class="text-teal-200 hover:text-white">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 8 19">
                            <path fill-rule="evenodd" d="M6.135 3H8V0H6.135a4.147 4.147 0 0 0-4.142 4.142V6H0v3h2v9.938h3V9h2.021l.592-3H5V3.591A.6.6 0 0 1 5.592 3h.543Z" clip-rule="evenodd"/>
                        </svg>
                    <span class="sr-only">Facebook page</span>
                </a>
                <a href="#" class="text-teal-200 hover:text-white">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 21 16">
                            <path d="M16.942 1.556a16.3 16.3 0 0 0-4.126-1.3 12.04 12.04 0 0 0-.529 1.1 15.175 15.175 0 0 0-4.573 0 11.585 11.585 0 0 0-.535-1.1 16.274 16.274 0 0 0-4.129 1.3A17.392 17.392 0 0 0 .182 13.218a15.785 15.785 0 0 0 4.963 2.521c.41-.564.773-1.16 1.084-1.785a10.63 10.63 0 0 1-1.706-.83c.143-.106.283-.217.418-.33a11.664 11.664 0 0 0 10.118 0c.137.113.277.224.418.33-.544.328-1.116.606-1.71.832a12.52 12.52 0 0 0 1.084 1.785 16.46 16.46 0 0 0 5.064-2.595 17.286 17.286 0 0 0-2.973-11.59ZM6.678 10.813a1.941 1.941 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.919 1.919 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Zm6.644 0a1.94 1.94 0 0 1-1.8-2.045 1.93 1.93 0 0 1 1.8-2.047 1.918 1.918 0 0 1 1.8 2.047 1.93 1.93 0 0 1-1.8 2.045Z"/>
                        </svg>
                    <span class="sr-only">Instagram page</span>
                </a>
                <a href="#" class="text-teal-200 hover:text-white">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 17">
                        <path fill-rule="evenodd" d="M19.7 3.036a2.49 2.49 0 0 0-1.753-1.754C16.31 1 10 1 10 1s-6.31 0-7.947.282A2.49 2.49 0 0 0 .253 3.036 26.02 26.02 0 0 0 0 7s0 3.964.253 4.964a2.49 2.49 0 0 0 1.753 1.754C3.69 14 10 14 10 14s6.31 0 7.947-.282a2.49 2.49 0 0 0 1.753-1.754A26.02 26.02 0 0 0 20 7s0-3.964-.3-4.964Zm-11.49 6.177V4.787l4.787 2.213-4.787 2.193Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="sr-only">YouTube channel</span>
                </a>
            </div>
        </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>