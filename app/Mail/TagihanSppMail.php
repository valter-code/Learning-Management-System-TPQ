<?php

namespace App\Mail;

use App\Models\Spp;
use App\Models\User; // Untuk info wali dari profil santri
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class TagihanSppMail extends Mailable
{
    use Queueable, SerializesModels;

    public Spp $spp;
    public ?User $wali; // Model User untuk data wali

    /**
     * Create a new message instance.
     */
    public function __construct(Spp $spp)
    {
        $this->spp = $spp;
        // Asumsi email wali ada di User model santri atau SantriProfile
        // Jika di SantriProfile, Anda perlu relasi dari User (santri) ke SantriProfile
        // $this->wali = $spp->santri->santriProfile; // Contoh jika ada relasi santriProfile di User
        // Untuk contoh ini, kita akan coba ambil email dari user santri jika itu email wali,
        // atau Anda perlu logika untuk mendapatkan email wali yang benar.
        // Idealnya, PendaftarSantri menyimpan email_wali, dan saat aktivasi, email_wali ini bisa
        // disimpan ke User model santri atau SantriProfile.
        // Untuk sekarang, kita coba email dari User santri (yang bisa jadi email wali)
         $this->wali = $spp->santri; // Asumsi email wali ada di $spp->santri->email atau $spp->santri->santriProfile->email_wali
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $namaWali = $this->wali->santriProfile?->nama_wali ?? $this->wali->name; // Ambil nama wali
        $emailWali = $this->wali->santriProfile?->email_wali ?? $this->wali->email; // Ambil email wali

        if (empty($emailWali)) {
            // Handle jika email wali tidak ada, mungkin tidak kirim atau log error
            // Untuk saat ini, kita return null agar tidak error, tapi email tidak terkirim
            return new Envelope(subject: 'Informasi SPP Santri'); // Subject default jika email tidak valid
        }

        return new Envelope(
            to: [new Address($emailWali, $namaWali)],
            subject: 'Pemberitahuan Tagihan SPP Santri - ' . $this->spp->santri->name,
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
                'namaSantri' => $this->spp->santri->name,
                'bulan' => $this->spp->nama_bulan,
                'tahun' => $this->spp->tahun,
                'jumlah' => number_format($this->spp->jumlah_bayar, 0, ',', '.'),
                // Tambahkan info cara pembayaran jika ada
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
