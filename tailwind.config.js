// tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
  content: [
      // Path Laravel default (biarkan ini selalu ada):
      './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
      './storage/framework/views/*.php',
      './resources/views/**/*.blade.php',
      './resources/js/**/*.js',
      './resources/js/**/*.ts',
      './app/View/Components/**/*.php',
      './app/Livewire/**/*.php',

      // Path Filament (PASTIKAN SEMUA INI ADA!):
      './app/Filament/**/*.php',                  // Kode PHP Filament Anda
      './resources/views/filament/**/*.blade.php', // Custom Blade untuk Filament
      './vendor/filament/**/*.blade.php',         // Ini KRUSIAL untuk CSS bawaan Filament
      // Tambahkan juga jika Anda menggunakan plugin Filament pihak ketiga:
      './vendor/**/**/*.blade.php', // Ini mencakup semua views di vendor, mungkin terlalu luas tapi aman
      // Atau lebih spesifik jika tahu nama vendor/plugin:
      // './vendor/awcodes/filament-badgeable/resources/views/**/*.blade.php', 
  ],
  theme: {
      extend: {},
  },
  plugins: [],
};