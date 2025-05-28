<?php

namespace App\Mail;

use App\Models\User; // Model User untuk data santri
use App\Models\PendaftarSantri; // Model PendaftarSantri untuk email wali
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class SantriBaruDiterimaMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $userSantri;
    public string $passwordDefault;
    public PendaftarSantri $pendaftar;

    /**
     * Create a new message instance.
     */
    public function __construct(User $userSantri, string $passwordDefault, PendaftarSantri $pendaftar)
    {
        $this->userSantri = $userSantri;
        $this->passwordDefault = $passwordDefault;
        $this->pendaftar = $pendaftar;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // Ambil alamat email wali dari data pendaftar
            to: [new Address($this->pendaftar->email_wali, $this->pendaftar->nama_wali)],
            subject: 'Selamat! Pendaftaran Santri Telah Diterima - LMS TPQ Anda',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Buat file Blade view untuk konten email
        return new Content(
            markdown: 'emails.diterima', // Path ke view Blade: resources/views/emails/santri/diterima.blade.php
            with: [
                'namaSantri' => $this->userSantri->name,
                'emailSantri' => $this->userSantri->email, // Email yang dibuat untuk santri
                'passwordDefault' => $this->passwordDefault,
                'namaWali' => $this->pendaftar->nama_wali,
                'urlLogin' => route('filament.santri.auth.login'), // Atau URL login panel santri Anda
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