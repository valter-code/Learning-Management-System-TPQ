<?php

namespace App\Http\Controllers;

use App\Models\PendaftarSantri;
use Illuminate\Http\Request;
use App\Enums\StatusPendaftaranSantri;
use Illuminate\Support\Facades\Mail; // <-- Import Mail facade
use App\Mail\PendaftaranSantriDiterimaWaliMail; // <-- Import Mailable baru Anda
use Illuminate\Support\Facades\Log; // <-- Import Log facade

class RegistrasiSantriController extends Controller
{
    public function create()
    {
        Log::info('Menampilkan halaman form registrasi santri.');
        return view('registrasi'); 
    }

    public function store(Request $request)
    {
        Log::info('Proses store registrasi santri dimulai.', ['request_data' => $request->all()]);

        $validatedData = $request->validate([
            'nama_lengkap_calon_santri' => 'required|string|max:255',
            'tempat_lahir_calon_santri' => 'nullable|string|max:255',
            'tanggal_lahir_calon_santri' => 'required|date',
            'jenis_kelamin_calon_santri' => 'required|in:laki-laki,perempuan',
            'alamat_calon_santri' => 'nullable|string',
            'asal_sekolah_calon_santri' => 'nullable|string|max:255',
            'nisn_calon_santri' => 'nullable|string|max:50|unique:pendaftar_santri,nisn_calon_santri',
            'nama_wali' => 'required|string|max:255',
            'nomor_telepon_wali' => 'required|string|max:20',
            'email_wali' => 'required|email|max:255', // Jadikan email wali wajib untuk notifikasi
            'pekerjaan_wali' => 'nullable|string|max:255',
            'catatan_tambahan' => 'nullable|string',
            'persetujuan' => 'required|accepted',
        ]);
        Log::info('Validasi data pendaftaran berhasil.');

        unset($validatedData['persetujuan']);
        
        // Status default sudah diatur di model PendaftarSantri atau bisa juga di sini:
        // $validatedData['status_pendaftaran'] = StatusPendaftaranSantri::PENDING->value;

        Log::info('Mencoba membuat record PendaftarSantri dengan data:', $validatedData);
        $pendaftar = PendaftarSantri::create($validatedData);
        Log::info('Record PendaftarSantri berhasil dibuat dengan ID: ' . $pendaftar->id);

        // Kirim Email Konfirmasi Pendaftaran ke Wali
        if (!empty($pendaftar->email_wali)) {
            Log::info('Mencoba mengirim email konfirmasi pendaftaran ke wali: ' . $pendaftar->email_wali);
            try {
                Mail::to($pendaftar->email_wali)->send(new PendaftaranSantriDiterimaWaliMail($pendaftar));
                Log::info('Email konfirmasi pendaftaran BERHASIL dikirim ke: ' . $pendaftar->email_wali);
                // Anda bisa menambahkan flash message spesifik untuk email jika mau
                session()->flash('email_info', 'Email konfirmasi pendaftaran telah dikirim ke ' . $pendaftar->email_wali);
            } catch (\Exception $e) {
                Log::error('GAGAL mengirim email konfirmasi pendaftaran ke ' . $pendaftar->email_wali . ': ' . $e->getMessage(), [
                    'exception_trace' => $e->getTraceAsString() // Untuk detail error
                ]);
                // Jangan hentikan proses hanya karena email gagal, tapi beri tahu user/admin
                // Anda bisa menambahkan flash message error email di sini jika mau
                // session()->flash('email_error', 'Gagal mengirim email konfirmasi. Namun pendaftaran Anda tetap tercatat.');
            }
        } else {
            Log::warning('Email wali tidak tersedia untuk pendaftar ID: ' . $pendaftar->id . '. Email konfirmasi tidak dikirim.');
        }

        return redirect()->route('registrasi.santri.create') // Atau ke halaman sukses jika ada
                         ->with('success', 'Pendaftaran berhasil! Data Anda telah kami terima dan sedang diproses. Mohon periksa email Anda untuk konfirmasi.');
    }
    
    // Anda bisa membuat rute dan view untuk halaman sukses
    // public function sukses()
    // {
    //     return view('public.registrasi.sukses'); 
    // }
}