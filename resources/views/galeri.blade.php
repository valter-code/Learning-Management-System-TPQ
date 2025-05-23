<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS TPQ [Nama TPQ Anda] - Pendidikan Al-Qur'an Berkualitas</title>

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
            margin-top: 2rem; /* Sesuaikan jarak atas */
            margin-bottom: 2rem; Sesuaikan jarak bawah
        } */
        /* Jika ingin pembatas hitam pekat */
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
        <span class="self-center text-2xl font-semibold whitespace-nowrap text-teal-700">LMS TPQ</span>
    </a>
    <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
        <button type="button" class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-4 py-2 text-center transition duration-150 ease-in-out">Daftar Sekarang!</button>
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
            <a href="#" class="block py-2 px-3 md:p-0 text-teal-600 rounded-sm md:bg-transparent" aria-current="page">Beranda</a>
        </li>
        <li>
            <a href="#pengumuman" class="block py-2 px-3 md:p-0 text-gray-700 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Pengumuman</a>
        </li>
        <li>
            <a href="#galeri" class="block py-2 px-3 md:p-0 text-gray-700 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Galeri</a>
        </li>
        <li>
            <a href="#tentang" class="block py-2 px-3 md:p-0 text-gray-700 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Tentang</a>
        </li>
        <li>
            <a href="#kontak" class="block py-2 px-3 md:p-0 text-gray-700 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Hubungi Kami</a>
        </li>
        </ul>
    </div>
    </div>
    </nav>



    <main>
    {{-- SECTION HERO --}}

    <div class="section-divider max-w-screen-xl mx-auto px-4"></div>

{{-- SECTION HEADER GALERI BARU --}}

  {{-- SECTION HEADER PENGUMUMAN (Menempel di bawah navbar sticky) --}}
  <section id="pengumuman-header" class="bg-teal-50">
            <div class="max-w-screen-xl mx-auto px-4 py-8 md:py-12"> 
                <div class="md:flex md:items-start md:justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-teal-800 sm:text-4xl">Galeri</h1>
                        <p class="mt-2 text-lg text-gray-700 max-w-3xl">
                        Galeri aktivitas yang dilakukan oleh oleh Pengajar dna Santri di TPQ Lorem Ipsum
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <nav aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2 text-sm font-medium text-gray-600">
                                <li><a href="#" class="hover:text-teal-600">Beranda</a></li>
                                <li>
                                    <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                </li>
                                <li><span class="text-teal-700" aria-current="page">Galeri</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </section>

        

    <div class="section-divider max-w-screen-xl mx-auto px-4"></div>

    {{-- SECTION GALERI BARU --}}
    <section id="galeri" class="py-16 bg-gray-50 px-4"> 
        <div class="max-w-screen-xl mx-auto"> 
            <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-center text-teal-800 lg:text-4xl">Galeri Kegiatan</h2>
            <p class="mb-12 text-lg text-center text-gray-600">Momen-momen berharga dari berbagai kegiatan di TPQ kami.</p>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach ($daftarGaleri as $galeri)
                {{-- Contoh Item Galeri 1 --}}
                <div class="group relative">
                    <img class="h-auto max-w-full rounded-lg shadow-md group-hover:shadow-xl transition-shadow duration-300 object-cover aspect-square" src="storage/{{ $galeri->image }}" alt="{{ $galeri->caption }}">
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg">
                        <p class="text-white text-center text-sm p-2">{{ $galeri->caption }}</p>
                    </div>
                </div>
                {{-- Tambahkan lebih banyak item galeri sesuai kebutuhan --}}
                @endforeach
            </div>
            {{-- Tombol Lihat Semua Foto (Opsional) --}}
            <div class="text-center mt-12">
                <a href="#" class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-6 py-3 transition duration-150 ease-in-out">
                    Lihat Semua Foto
                </a>
            </div>
        </div>
    </section>

    <div class="section-divider max-w-screen-xl mx-auto px-4"></div> 

</main>

    <footer id="kontak" class="bg-teal-700 text-white"> 
        <div class="mx-auto w-full max-w-screen-xl p-4 py-10 lg:py-12"> 
            <div class="md:flex md:justify-between">
            <div class="mb-8 md:mb-0"> 
                <a href="#" class="flex items-center">
                    <img src="https://placehold.co/40x40/FFFFFF/0D9488?text=TPQ" class="h-8 me-3 rounded-md" alt="LMS TPQ Logo" /> 
                    <span class="self-center text-2xl font-semibold whitespace-nowrap">LMS TPQ</span>
                </a>
                 <p class="mt-4 text-sm text-white max-w-xs"> 
                    Jl. Pendidikan Al-Qur'an No. 123<br>
                    Kelurahan Mengaji, Kecamatan Iqra<br>
                    Jakarta, 12345<br>
                    <a href="tel:0211234567" class="hover:underline text-white hover:text-teal-50">Telp: (021) 123-4567</a><br>
                    <a href="mailto:info@tpqanda.sch.id" class="hover:underline text-white hover:text-teal-50">Email: info@tpqanda.sch.id</a>
                </p>
            </div>
            <div class="grid grid-cols-2 gap-8 sm:gap-10 sm:grid-cols-3"> 
                <div>
                    <h2 class="mb-6 text-sm font-semibold uppercase text-white">Navigasi</h2>
                    <ul class="text-teal-100 font-medium">
                        <li class="mb-4">
                            <a href="#" class="text-white hover:underline hover:text-white">Beranda</a>
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
            <span class="text-sm text-black sm:text-center">© {{ date('Y') }} <a href="#" class="hover:underline hover:text-black">LMS TPQ Lorem Ipsum™</a>. Hak Cipta Dilindungi.
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