<?php

namespace App\Mail;

use App\Models\PendaftarSantri; // Model PendaftarSantri
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class PendaftaranSantriDiterimaWaliMail extends Mailable
{
    use Queueable, SerializesModels;

    public PendaftarSantri $pendaftar;

    /**
     * Create a new message instance.
     */
    public function __construct(PendaftarSantri $pendaftar)
    {
        $this->pendaftar = $pendaftar;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: [new Address($this->pendaftar->email_wali, $this->pendaftar->nama_wali)],
            subject: 'Konfirmasi Pendaftaran Santri - LMS TPQ Anda',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Buat file Blade view untuk konten email ini
        return new Content(
            markdown: 'emails.diterima-wali', // Path: resources/views/emails/pendaftaran/diterima-wali.blade.php
            with: [
                'namaWali' => $this->pendaftar->nama_wali,
                'namaCalonSantri' => $this->pendaftar->nama_lengkap_calon_santri,
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