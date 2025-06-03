<x-mail::message>
{{-- SALUTATION --}}
Assalamu'alaikum Wr. Wb. Bapak/Ibu {{ $namaWali }},

<br>

Dengan hormat kami sampaikan pemberitahuan tagihan Sumbangan Pembinaan Pendidikan (SPP) untuk Ananda:
<br>
**{{ $namaSantri }}**

<br>

---

{{-- CURRENT PERIOD BILL DETAILS --}}
<x-mail::panel>
**RINCIAN TAGIHAN UTAMA (PERIODE SAAT INI)**
<br>
- **Periode:** Bulan {{ $sppTrigger->nama_bulan }} {{ $sppTrigger->tahun }}
- **Jumlah Tagihan:** Rp {{ $biayaSppTriggerFormatted }}
</x-mail::panel>

{{-- ARREARS INFORMATION (IF ANY) --}}
@if ($countUnpaidMonths > 1 || ($countUnpaidMonths == 1 && optional($unpaidSppsDetails->first())->id != $spp->id && $spp->status_pembayaran != \App\Enums\StatusSpp::LUNAS->value) )
    {{-- This condition means:
        1. More than one month is unpaid ($countUnpaidMonths > 1), OR
        2. Exactly one month is unpaid ($countUnpaidMonths == 1), BUT it's NOT the $sppTrigger,
           AND the $sppTrigger itself ISN'T LUNAS (meaning $sppTrigger is a current bill,
           and there's one other different month unpaid).
        Basically, there are other unpaid amounts beyond just the current $sppTrigger.
    --}}
<x-mail::panel>
**INFORMASI KESELURUHAN TAGIHAN (TERMASUK TUNGGAKAN)**
<br>
Ananda tercatat memiliki total **{{ $countUnpaidMonths }} bulan** tagihan SPP yang perlu diselesaikan, dengan rincian sebagai berikut:
<ul>
@foreach ($unpaidPeriodsList as $period)
    <li>{{ $period }}</li>
@endforeach
</ul>
<br>
**Total Keseluruhan Tagihan yang Harus Dibayar** <br>
<span style="font-size: 1.2em; font-weight: bold;">Rp {{ $totalUnpaidAmountFormatted }}</span>
</x-mail::panel>
Mohon untuk dapat segera menyelesaikan seluruh kewajiban pembayaran SPP tersebut.
@else
    {{-- This means $countUnpaidMonths is 0, or 1 (and that 1 is the $sppTrigger and it's the only unpaid one).
         Since the email is triggered for an SPP, $countUnpaidMonths should ideally be at least 1. --}}
Tagihan untuk periode **Bulan {{ $sppTrigger->nama_bulan }} {{ $sppTrigger->tahun }}** di atas adalah satu-satunya kewajiban SPP Ananda saat ini.
Mohon untuk segera melakukan pembayaran SPP tersebut.
@endif

<br>

---

{{-- PAYMENT INSTRUCTIONS --}}
<x-mail::panel>
### **PETUNJUK PEMBAYARAN**

Anda dapat melakukan pembayaran SPP melalui salah satu metode berikut:

**A. TRANSFER BANK**
<br>
Mohon transfer ke rekening berikut:
<div style="margin-left: 20px; padding: 10px; border-left: 3px solid #007bff;">
    <strong>Bank:</strong> {{ $namaBankSetting }}<br>
    <strong>Nomor Rekening:</strong> <br>
    <span style="font-size: 1.5em; font-weight: bold; color: #D2691E;">{{ $nomorRekeningSetting }}</span><br>
    <strong>Atas Nama:</strong> {{ $atasNamaRekeningSetting }}
</div>
<br>
**Mohon pastikan untuk mencantumkan **Nama Santri** dan **Periode Pembayaran** (contoh: {{ $namaSantri }} - SPP {{ $sppTrigger->nama_bulan }} {{ $sppTrigger->tahun }}) pada bagian berita atau keterangan transfer untuk memudahkan proses verifikasi kami.**

<br>

**B. PEMBAYARAN LANGSUNG**
<br>
Silakan datang langsung ke kantor administrasi {{ config('app.name', 'Nama Institusi Anda') }}.
</x-mail::panel>

---

{{-- CONTACT INFORMATION & CLOSING --}}
Jika Bapak/Ibu sudah melakukan pembayaran atau membutuhkan informasi lebih lanjut silahkan membalas email ini.

Terima kasih atas perhatian dan kerjasamanya.
<br>
Wassalamu'alaikum Wr. Wb.

<br>
Hormat kami,<br>
{{ config('app.name', 'Nama Institusi Anda') }}
</x-mail::message>