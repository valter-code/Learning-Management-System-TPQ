<x-mail::message>
Pendaftaran Santri Diterima!

Assalamu'alaikum Wr. Wb. Bapak/Ibu {{ $namaWali }},

Alhamdulillah, kami informasikan bahwa pendaftaran putra/putri Anda:
Nama Santri: {{ $namaSantri }}

Telah diterima di LMS {{ config('app.name') }} kami.

Berikut adalah detail akun untuk Ananda agar dapat mengakses sistem pembelajaran kami:

Email Login Santri: {{ $emailSantri }}
Password Default: {{ $passwordDefault }}

Mohon untuk segera login dan mengganti password default demi keamanan.
Anda dapat login melalui tautan berikut:
<x-mail::button :url="$urlLogin">
Login ke Panel Santri
</x-mail::button>

Jika ada pertanyaan lebih lanjut, jangan ragu untuk menghubungi kami.

Terima kasih atas kepercayaan Anda.
Wassalamu'alaikum Wr. Wb.

Hormat kami,


{{ config('app.name') }}
</x-mail::message>