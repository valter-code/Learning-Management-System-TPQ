<?php

namespace App\Mail;

use App\Models\Spp;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User; // Untuk info wali dari profil santri
use App\Enums\StatusSpp; // Pastikan Enum StatusSpp di-import
use Illuminate\Support\Collection as EloquentCollection; // Untuk tipe data unpaidSpps

class TagihanSppMail extends Mailable
{
    use Queueable, SerializesModels;

    public Spp $spp; // SPP spesifik yang mentrigger email ini
    public User $santri;
    public ?User $wali; // Disimpan dari $spp->santri, yang mungkin memiliki relasi ke profil wali

    public EloquentCollection $unpaidSppsDetails;
    public int $countUnpaidMonths;
    public float $totalUnpaidAmount;
    public array $unpaidPeriodsList;

    /**
     * Create a new message instance.
     */
    public function __construct(Spp $spp)
    {
        $this->spp = $spp;
        $this->santri = $spp->santri; // Asumsi relasi santri ada di model Spp
        $this->wali = $spp->santri; // Digunakan untuk mendapatkan info wali dari santriProfile

        // Ambil semua SPP yang belum bayar atau terlambat untuk santri ini
        $this->unpaidSppsDetails = Spp::where('santri_id', $this->santri->id)
            ->whereIn('status_pembayaran', [StatusSpp::BELUM_BAYAR->value, StatusSpp::TERLAMBAT->value])
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        $this->namaBankSetting = Setting::where('key', 'pembayaran.nama_bank')->first()?->value ?? '[Nama Bank Belum Diatur]';
        $this->nomorRekeningSetting = Setting::where('key', 'pembayaran.nomor_rekening')->first()?->value ?? '[Nomor Rekening Belum Diatur]';
        $this->atasNamaRekeningSetting = Setting::where('key', 'pembayaran.atas_nama_rekening')->first()?->value ?? '[Atas Nama Belum Diatur]';
        $this->countUnpaidMonths = $this->unpaidSppsDetails->count();
        $this->totalUnpaidAmount = $this->unpaidSppsDetails->sum('biaya_bulanan'); // Menggunakan biaya_bulanan sebagai dasar tagihan per bulan

        $this->unpaidPeriodsList = $this->unpaidSppsDetails->map(function ($itemSpp) {
            return $itemSpp->nama_bulan . ' ' . $itemSpp->tahun . ' (Rp ' . number_format($itemSpp->biaya_bulanan, 0, ',', '.') . ')';
        })->toArray();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Mengambil nama dan email wali dari santriProfile yang terhubung dengan model User (santri)
        $namaWali = $this->wali->santriProfile?->nama_wali ?? $this->wali->name; // Fallback ke nama santri jika nama_wali tidak ada
        $emailWali = $this->wali->santriProfile?->email_wali ?? $this->wali->email; // Fallback ke email santri jika email_wali tidak ada

        if (empty($emailWali)) {
            // Idealnya, ini tidak boleh terjadi jika validasi data baik.
            // Anda bisa melempar exception atau log error di sini.
            // Untuk sementara, kita buat subject default agar tidak crash.
            return new Envelope(subject: 'Informasi SPP Santri (Email Wali Tidak Ditemukan)');
        }

        return new Envelope(
            to: [new Address($emailWali, $namaWali)],
            subject: 'Pemberitahuan Tagihan SPP Santri - ' . $this->santri->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.spp.tagihan', // Path: resources/views/emails/spp/tagihan.blade.php
            with: [
                'namaWali' => $this->wali->santriProfile?->nama_wali ?? $this->wali->name,
                'namaSantri' => $this->santri->name,
                'sppTrigger' => $this->spp, // SPP spesifik yang ditagihkan
                'biayaSppTriggerFormatted' => number_format($this->spp->biaya_bulanan, 0, ',', '.'),
                'unpaidPeriodsList' => $this->unpaidPeriodsList,
                'countUnpaidMonths' => $this->countUnpaidMonths,
                'totalUnpaidAmountFormatted' => number_format($this->totalUnpaidAmount, 0, ',', '.'),

                'namaBankSetting' => $this->namaBankSetting,
                'nomorRekeningSetting' => $this->nomorRekeningSetting,
                'atasNamaRekeningSetting' => $this->atasNamaRekeningSetting,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}