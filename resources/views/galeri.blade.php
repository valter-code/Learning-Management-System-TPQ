<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Kegiatan - {{ config('app.name') }}</title>
    <link rel="icon" href="https://placehold.co/32x32/10B981/FFFFFF?text=TPQ" type="image/png">
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
        body { font-family: 'Inter', sans-serif; }
        .photo-card {
            position: relative;
            overflow: hidden; 
            border-radius: 0.5rem; 
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); 
            transition: box-shadow 0.2s ease-in-out;
        }
        .photo-card:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); 
        }
        .photo-card .description-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.75); 
            color: white;
            padding: 0.75rem; 
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
            font-size: 0.875rem; 
            line-height: 1.25rem;
            max-height: 60%;
            overflow-y: auto; 
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
        .photo-card:hover .description-overlay {
            opacity: 1;
            visibility: visible;
        }
        .photo-card .description-overlay .kegiatan-title {
            font-weight: 600; 
            font-size: 1rem; 
            margin-bottom: 0.25rem; 
            display: block;
        }
        /* Ensure body overflow is handled by script, not default */
        /* body.overflow-hidden {
            overflow: hidden;
        } */
        .section-divider-dark {
            border-top: 1px solid #000000;
            margin-top: 3rem;
            margin-bottom: 3rem;
        }
    </style>
</head>
<body class="bg-gray-100 antialiased">

    {{-- NAVIGASI --}}
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="https://placehold.co/40x40/0D9488/FFFFFF?text=TPQ" class="h-8 rounded-md" alt="LMS TPQ Logo" />
            <span class="self-center text-2xl font-semibold whitespace-nowrap text-teal-700">{{ config('app.name') }}</span>
        </a>
        <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <a href="{{ route('registrasi.santri.create') }}" class="inline-block text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-4 py-2 text-center transition duration-150 ease-in-out">
            Daftar Sekarang!
            </a>

            <button data-collapse-toggle="navbar-cta" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-600 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200" aria-controls="navbar-cta" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/></svg>
            </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-cta">
            <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white">
                <li><a href="{{ url('/') }}" class="block py-2 px-3 md:p-0 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Beranda</a></li>
                <li><a href="pengumuman" class="block py-2 px-3 md:p-0 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Pengumuman</a></li>
                <li><a href="galeri" class="block py-2 px-3 md:p-0 text-teal-600 rounded md:bg-transparent" aria-current="page">Galeri</a></li>
                <li><a href="tentang" class="block py-2 px-3 md:p-0 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Tentang</a></li>
                <li><a href="kontak" class="block py-2 px-3 md:p-0 text-gray-700 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-teal-600">Hubungi Kami</a></li>
            </ul>
        </div>
        </div>
    </nav>

    <main>
        {{-- SECTION HEADER GALERI --}}
        <section id="galeri-header" class="bg-teal-50 py-10 px-4">
            <div class="max-w-screen-xl mx-auto">
                <div class="md:flex md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-extrabold tracking-tight text-teal-800 sm:text-4xl">Galeri Kegiatan</h1>
                        <p class="mt-3 text-lg text-gray-700 max-w-3xl">
                            Momen-momen berharga dari berbagai kegiatan di TPQ kami.
                        </p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <nav aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2 text-sm font-medium text-gray-600">
                                <li><a href="{{ url('/') }}" class="hover:text-teal-600">Beranda</a></li>
                                <li><svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                                <li><span class="text-teal-700" aria-current="page">Galeri</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </section>

        {{-- SECTION KONTEN GALERI --}}
        <section id="galeri-content" class="py-16 bg-white px-4">
            <div class="max-w-screen-xl mx-auto">
                {{-- Filter Dropdown --}}
                <div class="mb-8 md:mb-10">
                    <form action="{{-- route('galeri.index') --}}" method="GET" class="max-w-sm mx-auto md:max-w-md">
                        <label for="kegiatan_id_filter" class="block mb-2 text-sm font-medium text-gray-900">Filter Berdasarkan Kegiatan:</label>
                        <div class="flex">
                            <select id="kegiatan_id_filter" name="kegiatan_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5" onchange="this.form.submit()">
                                <option value="">Semua Kegiatan</option>
                                @php
                                    // Mock data for preview if not in Laravel context
                                    if (!isset($semuaKegiatanGaleriUntukFilter)) {
                                        $semuaKegiatanGaleriUntukFilter = [
                                            (object)['id' => 1, 'nama_kegiatan' => 'Kegiatan A'],
                                            (object)['id' => 2, 'nama_kegiatan' => 'Kegiatan B'],
                                        ];
                                        $selectedKegiatanId = 1;
                                    }
                                @endphp
                                @if(isset($semuaKegiatanGaleriUntukFilter))
                                    @foreach($semuaKegiatanGaleriUntukFilter as $kegiatanFilter)
                                        <option value="{{ $kegiatanFilter->id }}" {{ (isset($selectedKegiatanId) && $selectedKegiatanId == $kegiatanFilter->id) ? 'selected' : '' }}>
                                            {{ $kegiatanFilter->nama_kegiatan }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </form>
                </div>

                @php
                    // Mock data for preview if not in Laravel context
                    if (!isset($daftarTampilKegiatanGaleri)) {
                        $daftarTampilKegiatanGaleri = collect([
                            (object)[
                                'nama_kegiatan' => 'Contoh Kegiatan 1',
                                'deskripsi_kegiatan' => 'Ini adalah deskripsi untuk contoh kegiatan 1.',
                                'tanggal_publikasi' => now(),
                                'fotos' => collect([
                                    (object)['path_file' => 'https://placehold.co/600x400/10B981/FFFFFF?text=Foto+1', 'judul_foto' => 'Judul Foto 1', 'deskripsi_foto' => 'Deskripsi singkat foto 1.'],
                                    (object)['path_file' => 'https://placehold.co/600x400/0D9488/FFFFFF?text=Foto+2', 'judul_foto' => 'Judul Foto 2', 'deskripsi_foto' => null],
                                    (object)['path_file' => 'https://placehold.co/600x400/0F766E/FFFFFF?text=Foto+3', 'judul_foto' => null, 'deskripsi_foto' => 'Deskripsi singkat foto 3.'],
                                ])
                            ],
                            (object)[
                                'nama_kegiatan' => 'Contoh Kegiatan 2',
                                'deskripsi_kegiatan' => 'Ini adalah deskripsi untuk contoh kegiatan 2, sedikit lebih panjang untuk menguji tampilan.',
                                'tanggal_publikasi' => now()->subDays(5),
                                'fotos' => collect([
                                    (object)['path_file' => 'https://placehold.co/600x400/047857/FFFFFF?text=Foto+A', 'judul_foto' => 'Judul Foto A', 'deskripsi_foto' => 'Deskripsi foto A yang cukup panjang agar bisa menguji line-clamp dan overflow pada overlay deskripsi foto di kartu galeri.'],
                                    (object)['path_file' => 'https://placehold.co/600x400/065F46/FFFFFF?text=Foto+B', 'judul_foto' => 'Judul Foto B', 'deskripsi_foto' => 'Deskripsi foto B.'],
                                ])
                            ]
                        ]);
                         // Mock Storage facade for URLs if not in Laravel
                        if (!class_exists('Storage')) {
                            class Storage {
                                public static function url($path) {
                                    if (filter_var($path, FILTER_VALIDATE_URL)) {
                                        return $path; // If it's already a full URL (like placeholder)
                                    }
                                    return '/storage/' . $path; // Simulate storage path
                                }
                            }
                        }
                    }
                @endphp

                {{-- Variabel utama untuk loop adalah $daftarTampilKegiatanGaleri --}}
                @if(isset($daftarTampilKegiatanGaleri) && $daftarTampilKegiatanGaleri->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada galeri kegiatan</h3>
                        @if(isset($selectedKegiatanId) && $selectedKegiatanId)
                            @php
                                $kegiatanTerpilih = isset($semuaKegiatanGaleriUntukFilter) ? collect($semuaKegiatanGaleriUntukFilter)->firstWhere('id', $selectedKegiatanId) : null;
                            @endphp
                            <p class="mt-1 text-sm text-gray-500">Tidak ada galeri untuk kegiatan "{{ $kegiatanTerpilih ? $kegiatanTerpilih->nama_kegiatan : '' }}".</p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Silakan cek kembali nanti atau pilih kegiatan lain.</p>
                        @endif
                    </div>
                @elseif(isset($daftarTampilKegiatanGaleri) && $daftarTampilKegiatanGaleri->count() > 0)
                    <div class="space-y-12"> {{-- Memberi jarak antar blok kegiatan galeri --}}
                        @foreach($daftarTampilKegiatanGaleri as $kegiatan)
                            <div>
                                {{-- Menampilkan Info Kegiatan Galeri (Judul, Deskripsi) --}}
                                <div class="mb-6 text-center md:text-left">
                                    <h2 class="text-2xl font-bold tracking-tight text-teal-700 sm:text-3xl">{{ $kegiatan->nama_kegiatan }}</h2>
                                    @if($kegiatan->deskripsi_kegiatan)
                                    <p class="mt-2 text-md text-gray-600 max-w-3xl mx-auto md:mx-0">{{ $kegiatan->deskripsi_kegiatan }}</p>
                                    @endif
                                    <p class="mt-1 text-xs text-gray-500">
                                        Dipublikasikan: {{ $kegiatan->tanggal_publikasi ? (new \Carbon\Carbon($kegiatan->tanggal_publikasi))->translatedFormat('d F Y') : 'N/A' }}
                                    </p>
                                </div>

                                @if($kegiatan->fotos->isNotEmpty())
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                                        @foreach($kegiatan->fotos as $foto)
                                            @php
                                                // Menyiapkan deskripsi untuk modal. Mengambil deskripsi foto, 
                                                // fallback ke deskripsi kegiatan jika deskripsi foto tidak ada,
                                                // fallback ke pesan default jika keduanya tidak ada.
                                                $modal_photo_description = $foto->deskripsi_foto ?: ($kegiatan->deskripsi_kegiatan ?: 'Tidak ada deskripsi tambahan untuk gambar ini.');
                                            @endphp
                                            <div class="photo-card aspect-w-1 aspect-h-1 group">
                                                <img src="{{ Storage::url($foto->path_file) }}"
                                                     alt="{{ $foto->judul_foto ?: $kegiatan->nama_kegiatan }}"
                                                     class="w-full h-full object-cover rounded-lg shadow-md cursor-pointer transition-transform duration-300 group-hover:scale-105"
                                                     onclick="openImageModal('{{ Storage::url($foto->path_file) }}', '{{ htmlspecialchars($foto->judul_foto ?: $kegiatan->nama_kegiatan, ENT_QUOTES) }}', '{!! addslashes(str_replace(["\r", "\n"], '', $modal_photo_description)) !!}')">
                                                
                                                @if($foto->deskripsi_foto || $kegiatan->deskripsi_kegiatan)
                                                <div class="description-overlay">
                                                    <span class="kegiatan-title">{{ $kegiatan->nama_kegiatan }}</span>
                                                    @if($foto->judul_foto)
                                                        <p class="text-xs font-semibold mb-1">{{ $foto->judul_foto }}</p>
                                                    @endif
                                                    <p class="line-clamp-3">{!! $foto->deskripsi_foto ?: $kegiatan->deskripsi_kegiatan !!}</p>
                                                </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center md:text-left">Belum ada foto di galeri kegiatan ini.</p>
                                @endif
                            </div>
                            @if(!$loop->last)
                                <hr class="my-12 border-gray-200">
                            @endif
                        @endforeach
                    </div>

                    {{-- Paginasi Links (untuk $daftarTampilKegiatanGaleri) --}}
                    @if ($daftarTampilKegiatanGaleri instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-12">
                        {{ $daftarTampilKegiatanGaleri->appends(request()->query())->links() }}
                    </div>
                    @endif
                @else
                     <div class="text-center py-12">
                         <h3 class="mt-2 text-sm font-semibold text-gray-900">Tidak dapat memuat galeri kegiatan saat ini.</h3>
                    </div>
                @endif
            </div>
        </section>
    </main>

    {{-- Modal untuk menampilkan gambar lebih besar --}}
    <div id="imageModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-x-hidden overflow-y-auto">
        {{-- Modal Dialog --}}
        <div class="relative w-full max-w-4xl max-h-[90vh]"> 
            {{-- Konten Modal dengan latar belakang putih --}}
            <div class="relative bg-white rounded-lg shadow">
                {{-- Modal header --}}
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 id="imageModalTitle" class="text-xl font-semibold text-gray-900">
                        Judul Gambar
                    </h3>
                    <button type="button" onclick="closeImageModal()" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                {{-- Modal body --}}
                <div class="p-4 md:p-5 space-y-4">
                    <div class="flex justify-center items-center">
                        {{-- Gambar, max-h disesuaikan agar ada ruang untuk deskripsi --}}
                        <img id="modalImage" src="https://placehold.co/1200x800/CCCCCC/FFFFFF?text=Loading..." alt="Gambar diperbesar" class="w-auto max-h-[60vh] sm:max-h-[65vh] object-contain rounded-lg"> 
                    </div>
                    {{-- Kontainer Deskripsi --}}
                    <div class="pt-2">
                        <h4 class="text-md font-semibold text-gray-800 mb-1">Deskripsi:</h4>
                        <p id="modalImageDescription" class="text-sm text-gray-600 max-h-[15vh] overflow-y-auto">
                            Deskripsi gambar akan muncul di sini.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Backdrop --}}
    <div id="imageModalBackdrop" class="hidden fixed inset-0 z-[90] bg-gray-900/75 backdrop-blur-sm"></div>


    {{-- FOOTER --}}
     <footer id="kontak" class="bg-teal-700 text-white">
        <div class="mx-auto w-full max-w-screen-xl p-4 py-10 lg:py-12">
            <div class="md:flex md:justify-between">
            <div class="mb-8 md:mb-0">
                <a href="{{ url('/') }}" class="flex items-center">
                    <img src="https://placehold.co/40x40/FFFFFF/0D9488?text=TPQ" class="h-8 me-3 rounded-md" alt="LMS TPQ Logo" />
                    <span class="self-center text-2xl font-semibold whitespace-nowrap">{{-- config('app.name') --}} {{ config('app.name') }}</span>
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
                            <a href="{{ url('/') }}" class="text-white hover:underline hover:text-white">Beranda</a>
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
            <span class="text-sm text-gray-200 sm:text-center dark:text-gray-300">© {{ date('Y') }} <a href="#" class="hover:underline hover:text-white">{{-- config('app.name') --}}{{ config('app.name') }}™</a>. Hak Cipta Dilindungi.
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

    <script>
        const imageModal = document.getElementById('imageModal');
        const imageModalBackdrop = document.getElementById('imageModalBackdrop');
        const modalImage = document.getElementById('modalImage');
        const imageModalTitle = document.getElementById('imageModalTitle');
        const modalImageDescription = document.getElementById('modalImageDescription'); // Element untuk deskripsi di modal

        // Fungsi untuk membuka modal dengan gambar, judul, dan deskripsi
        function openImageModal(src, title, description) {
            modalImage.src = src;
            imageModalTitle.textContent = title || 'Detail Gambar';
            modalImageDescription.innerHTML = description || 'Tidak ada deskripsi tambahan.'; // Set teks deskripsi (mengizinkan HTML)            
            imageModal.classList.remove('hidden');
            imageModal.classList.add('flex'); // Pastikan flex aktif untuk centering
            imageModalBackdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // Mencegah scroll background
        }

        function closeImageModal() {
            imageModal.classList.add('hidden');
            imageModal.classList.remove('flex');
            imageModalBackdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden'); // Mengembalikan scroll background
            // Reset src gambar agar tidak menampilkan gambar sebelumnya saat modal dibuka lagi sebelum gambar baru dimuat
            modalImage.src = 'https://placehold.co/1200x800/CCCCCC/FFFFFF?text=Loading...'; 
        }

        // Menutup modal ketika mengklik backdrop
        imageModalBackdrop.addEventListener('click', closeImageModal);

        // Menutup modal dengan tombol Escape
        document.addEventListener('keydown', function (event) {
            if (event.key === "Escape" && !imageModal.classList.contains('hidden')) {
                closeImageModal();
            }
        });

        // Untuk toggle Navbar mobile (jika tidak ditangani oleh file JS terpisah seperti flowbite)
        const navbarToggle = document.querySelector('[data-collapse-toggle="navbar-cta"]');
        const navbarCta = document.getElementById('navbar-cta');

        if (navbarToggle && navbarCta) {
            navbarToggle.addEventListener('click', function() {
                const expanded = navbarToggle.getAttribute('aria-expanded') === 'true' || false;
                navbarToggle.setAttribute('aria-expanded', !expanded);
                navbarCta.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>
