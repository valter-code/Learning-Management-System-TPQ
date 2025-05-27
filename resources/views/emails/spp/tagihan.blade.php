    <x-mail::message>
    # Pemberitahuan Tagihan SPP

    Assalamu'alaikum Wr. Wb. Bapak/Ibu {{ $namaWali }},

    Kami informasikan bahwa terdapat tagihan SPP untuk Ananda:
    **Nama Santri:** {{ $namaSantri }}
    **Periode:** Bulan {{ $bulan }} {{ $tahun }}
    **Jumlah Tagihan:** Rp {{ $jumlah }}

    Mohon untuk segera melakukan pembayaran SPP tersebut.
    {{-- Tambahkan informasi cara pembayaran di sini --}}
    <x-mail::panel>
    Informasi Cara Pembayaran:
    - Transfer ke rekening Bank XYZ: 123-456-7890 a.n. TPQ Anda
    - Atau pembayaran langsung di kantor administrasi TPQ.
    </x-mail::panel>

    Jika sudah melakukan pembayaran, mohon konfirmasi ke bagian administrasi kami.

    Terima kasih atas perhatian dan kerjasamanya.
    Wassalamu'alaikum Wr. Wb.

    Hormat kami,<br>
    {{ config('app.name') }}
    </x-mail::message>
    