<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Pendidikan Al-Qur'an Berkualitas</title>

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
        <span class="self-center text-2xl font-semibold whitespace-nowrap text-teal-700">{{ config('app.name') }}</span>
    </a>
    <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
        <a href="{{ route('registrasi.santri.create') }}" class="inline-block text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-4 py-2 text-center transition duration-150 ease-in-out">
        Daftar Sekarang!
</a>

</button>

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
            <a href="pengumuman" class="block py-2 px-3 md:p-0 text-gray-700 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Pengumuman</a>
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

    <main>
        <section class="bg-gradient-to-br from-teal-50 via-emerald-50 to-white py-20 px-4 text-center">
            <div class="max-w-screen-md mx-auto">
                <h1 class="mb-5 text-4xl font-extrabold leading-tight tracking-tight text-teal-800 md:text-5xl lg:text-6xl">
                    Selamat Datang di {{ config('app.name') }}
                </h1>
                <p class="mb-10 text-lg font-normal text-gray-700 lg:text-xl">
                    {{ config('app.name') }} Tempat terbaik untuk belajar karena dibimbing oleh Imam Dolor Sit Amet untuk membaca, memahami, dan mengamalkan Al-Qur'an dengan metode yang efektif dan menyenangkan.
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

        <section id="tentang" class="py-16 bg-gray-50 px-4"> 
            <div class="max-w-screen-xl mx-auto"> 
                <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-center text-teal-800 lg:text-4xl">Tentang Kami</h2>
                <p class="mb-12 text-lg text-center text-gray-600">Membangun Generasi Qur'ani Berakhlak Mulia</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center"> 
                    <div class="md:order-1"> 
                        <img src="https://placehold.co/600x400/A7F3D0/065F46?text=Ilustrasi+Belajar+Mengaji" alt="Ilustrasi Belajar Mengaji" class="mx-auto rounded-lg shadow-xl w-full max-w-md">
                    </div>
                    <div class="md:order-2 space-y-8"> 
                        <div class="p-6 bg-teal-50 rounded-lg border border-teal-200 shadow-lg flex flex-col"> 
                             <div class="flex items-center mb-3">
                                 <span class="inline-flex items-center justify-center p-2 rounded-md bg-teal-100 mr-3">
                                     <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </span>
                                <h3 class="text-xl font-semibold text-teal-700">Visi</h3>
                             </div>
                            <p class="text-gray-700 flex-grow">Menjadi pusat pendidikan Al-Qur'an yang terpercaya dalam mencetak generasi yang fasih membaca, memahami kandungan, serta mengamalkan ajaran Al-Qur'an dalam kehidupan sehari-hari.</p>
                        </div>
                         <div class="p-6 bg-emerald-50 rounded-lg border border-emerald-200 shadow-lg flex flex-col">  
                             <div class="flex items-center mb-3">
                                <span class="inline-flex items-center justify-center p-2 rounded-md bg-emerald-100 mr-3">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </span>
                                <h3 class="text-xl font-semibold text-emerald-700">Misi</h3>
                             </div>
                            <ul class="list-disc list-inside text-gray-700 space-y-1 flex-grow">
                                <li>Menyediakan kurikulum Tahsin & Tahfidz yang terstruktur.</li>
                                <li>Menerapkan metode pembelajaran yang interaktif dan efektif.</li>
                                <li>Membina akhlak dan adab Islami para santri.</li>
                                <li>Menjalin komunikasi aktif dengan wali santri.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="section-divider-black max-w-screen-xl mx-auto px-4"></div> 

        <section id="testimoni" class="py-16 bg-gray-100 px-4"> 
            <div class="max-w-screen-lg mx-auto">
                <h2 class="mb-4 text-3xl font-extrabold tracking-tight text-center text-teal-800 lg:text-4xl">Testimoni & Pengalaman</h2>
                 <p class="mb-12 text-lg text-center text-gray-600">Dengarkan langsung dari mereka yang telah merasakan manfaat belajar di TPQ kami.</p>

                <div class="grid gap-8 lg:grid-cols-3">
                     <figure class="flex flex-col p-6 bg-white shadow-lg rounded-lg border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                        <figcaption class="flex items-center space-x-4 rtl:space-x-reverse mb-4">
                            <img class="w-12 h-12 rounded-full object-cover" src="https://placehold.co/80x80/3B82F6/FFFFFF?text=AD" alt="Foto Bapak Adi">
                            <div>
                                <div class="font-semibold text-gray-900">Bapak Adi</div>
                                <div class="text-sm text-gray-500">Wali Santri Rahman</div>
                            </div>
                        </figcaption>
                        <blockquote class="flex-grow">
                            <p class="text-base font-normal text-gray-700">"Perkembangan Rahman sangat pesat, terutama dalam hal kelancaran membaca Qur'an dan adabnya. Pengajarnya sangat telaten."</p>
                        </blockquote>
                    </figure>
                    <figure class="flex flex-col p-6 bg-white shadow-lg rounded-lg border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                        <figcaption class="flex items-center space-x-4 rtl:space-x-reverse mb-4">
                            <img class="w-12 h-12 rounded-full object-cover" src="https://placehold.co/80x80/10B981/FFFFFF?text=ST" alt="Foto Siti">
                            <div>
                                <div class="font-semibold text-gray-900">Siti Aminah</div>
                                <div class="text-sm text-gray-500">Alumni TPQ 2022</div>
                            </div>
                        </figcaption>
                        <blockquote class="flex-grow">
                             <p class="text-base font-normal text-gray-700">"Belajar di sini bukan hanya soal mengaji, tapi juga membentuk karakter. Saya mendapatkan banyak ilmu dan teman baik."</p>
                        </blockquote>
                    </figure>
                    <figure class="flex flex-col p-6 bg-white shadow-lg rounded-lg border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                        <figcaption class="flex items-center space-x-4 rtl:space-x-reverse mb-4">
                            <img class="w-12 h-12 rounded-full object-cover" src="https://placehold.co/80x80/065F46/FFFFFF?text=DR" alt="Foto Ibu Dewi R.">
                            <div>
                                <div class="font-semibold text-gray-900">Ibu Dewi R.</div>
                                <div class="text-sm text-gray-500">Wali Santri Kayla</div>
                            </div>
                        </figcaption>
                         <blockquote class="flex-grow">
                            <p class="text-base font-normal text-gray-700">"Fasilitas LMS online-nya sangat membantu kami memantau perkembangan Kayla. Komunikasinya juga sangat baik."</p>
                        </blockquote>
                    </figure>
                </div>
            </div>
        </section>

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