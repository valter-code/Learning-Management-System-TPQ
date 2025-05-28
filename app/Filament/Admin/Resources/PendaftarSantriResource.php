<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Enums\UserRole;
use Filament\Infolists;
// StatusSpp tidak digunakan di sini, bisa dihapus jika tidak ada referensi lain
// use App\Enums\StatusSpp; 
use Filament\Forms\Form;
use Filament\Tables\Table;
// TagihanSppMail tidak digunakan di sini
// use App\Mail\TagihanSppMail; 
use App\Models\SantriProfile;
use App\Models\PendaftarSantri;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Enums\StatusPendaftaranSantri;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str; 
use App\Filament\Admin\Resources\PendaftarSantriResource\Pages;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Collection; 
use App\Mail\SantriBaruDiterimaMail; 

class PendaftarSantriResource extends Resource
{
    protected static ?string $model = PendaftarSantri::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Pendaftar Santri Baru';
    protected static ?string $modelLabel = 'Pendaftar Santri';
    protected static ?string $pluralModelLabel = 'Pendaftar Santri';
    protected static ?string $slug = 'pendaftar-santri';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationBadgeTooltip = 'Jumlah pendaftar santri yang perlu diproses';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByRaw("
                CASE status_pendaftaran
                    WHEN '" . StatusPendaftaranSantri::PENDING->value . "' THEN 1
                    WHEN '" . StatusPendaftaranSantri::DIPROSES->value . "' THEN 2
                    ELSE 3
                END ASC
            ")
            ->orderBy('created_at', 'desc');
    }

    public static function getNavigationBadge(): ?string
    {
        $statusPending = StatusPendaftaranSantri::PENDING->value;
        $statusDiproses = StatusPendaftaranSantri::DIPROSES->value;

        $count = static::getModel()::whereIn('status_pendaftaran', [$statusPending, $statusDiproses])->count();
        return $count > 0 ? (string) $count : null;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status_pendaftaran')
                    ->label('Status Pendaftaran')
                    ->options(StatusPendaftaranSantri::class)
                    ->required()
                    ->native(false)
                    ->live() // live() bisa tetap ada jika ada field lain yang bergantung padanya di form
                    // Hapus afterStateUpdated dari sini
                    ->default(StatusPendaftaranSantri::PENDING),
                Forms\Components\Textarea::make('catatan_admin')
                    ->label('Catatan Admin (Internal)')
                    ->columnSpanFull(),
                Forms\Components\Fieldset::make('Data Pendaftar (Read-only)')
                    ->columns(2)
                    ->disabled()
                    ->schema([
                        Forms\Components\TextInput::make('nama_lengkap_calon_santri')->label('Nama Calon Santri'),
                        Forms\Components\TextInput::make('nisn_calon_santri')->label('NISN')->placeholder('N/A'),
                        Forms\Components\DatePicker::make('tanggal_lahir_calon_santri')->label('Tanggal Lahir'),
                        Forms\Components\TextInput::make('jenis_kelamin_calon_santri')->label('Jenis Kelamin')->formatStateUsing(fn($state) => is_string($state) ? ucfirst($state) : $state),
                        Forms\Components\Textarea::make('alamat_calon_santri')->label('Alamat')->columnSpanFull()->placeholder('N/A'),
                        Forms\Components\TextInput::make('nama_wali')->label('Nama Wali'),
                        Forms\Components\TextInput::make('nomor_telepon_wali')->label('No. Telepon Wali'),
                        Forms\Components\TextInput::make('email_wali')->label('Email Wali')->placeholder('N/A'),
                        Forms\Components\TextInput::make('pekerjaan_wali')->label('Pekerjaan Wali')->placeholder('N/A'),
                        Forms\Components\Textarea::make('catatan_tambahan')->label('Catatan Tambahan Pendaftar')->columnSpanFull()->placeholder('N/A'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap_calon_santri')->label('Nama Calon Santri')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama_wali')->label('Nama Wali')->searchable(),
                Tables\Columns\TextColumn::make('nomor_telepon_wali')->label('No. Telepon Wali'),
                Tables\Columns\TextColumn::make('status_pendaftaran')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof StatusPendaftaranSantri ? $state->getLabel() : $state)
                    ->color(fn ($state) => $state instanceof StatusPendaftaranSantri ? $state->getColor() : 'gray')
                    ->sortable(), 
                Tables\Columns\TextColumn::make('created_at')->label('Tgl Daftar')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_pendaftaran')
                    ->options(StatusPendaftaranSantri::class)
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('Ubah Status/Catatan')
                    // ->modal() // EditAction defaultnya sudah modal jika halaman edit tidak ada/tidak dipanggil
                    ->after(function (PendaftarSantri $record, array $data) {
                        // $record adalah instance PendaftarSantri SETELAH diupdate dengan $data
                        Log::info("EditAction - afterSave. Pendaftar ID: {$record->id}, Status baru dari DB: {$record->status_pendaftaran->value}");
                        
                        // Cek apakah status diubah menjadi AKTIF
                        if ($record->status_pendaftaran === StatusPendaftaranSantri::AKTIF) {
                            // Panggil metode aktivasi. Parameter kedua (true) adalah untuk mengirim notifikasi.
                            // Metode processSantriActivation akan menghapus $record jika berhasil.
                            static::processSantriActivation($record, true, true); // Parameter ketiga untuk menandakan ini dari EditAction
                        }
                    }),
                Tables\Actions\Action::make('aktivasiSantri')
                    ->label('Aktifkan Santri')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aktifkan Pendaftaran Santri?')
                    ->modalDescription('Setelah diaktifkan, akun pengguna akan dibuat dan pendaftar akan dihapus. Email notifikasi akan dikirim.')
                    ->action(function (PendaftarSantri $record) {
                        static::processSantriActivation($record, true, false); // Parameter ketiga false (bukan dari EditAction)
                    })
                    ->visible(fn (PendaftarSantri $record) => 
                        $record->status_pendaftaran === StatusPendaftaranSantri::PENDING || 
                        $record->status_pendaftaran === StatusPendaftaranSantri::DIPROSES
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        // ... (definisi infolist Anda tetap sama) ...
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pendaftar') /* ... */,
                Infolists\Components\Section::make('Informasi Wali') /* ... */,
                Infolists\Components\Section::make('Status Pendaftaran') /* ... */,
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftarSantris::route('/'),
            'create' => Pages\CreatePendaftarSantri::route('/create'), // Admin bisa input manual
            // 'edit' => Pages\EditPendaftarSantri::route('/{record}/edit'), // Biarkan ini agar modal EditAction bisa menggunakan form default
            'view' => Pages\ViewPendaftarSantri::route('/{record}'),
        ];
    }

    // Otorisasi
    public static function canViewAny(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canCreate(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canEdit(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canDelete(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }

    // Metode processSantriActivation disesuaikan untuk menghapus record pendaftar
    protected static function processSantriActivation(PendaftarSantri $record, bool $sendNotification = true, bool $fromEditAction = false): void
    {
        // Jika dipanggil dari EditAction, status sudah diubah menjadi AKTIF oleh form save.
        // Jika dipanggil dari tombol 'aktivasiSantri', status mungkin masih PENDING/DIPROSES.
        // Kita cek apakah user sudah ada untuk mencegah duplikasi jika proses ini terpicu lebih dari sekali.

        $emailCalonSantri = $record->email_wali ?? static::generateTempEmail($record);
        $existingUser = User::where('email', $emailCalonSantri)->first();

        if ($existingUser && $existingUser->role === UserRole::SANTRI) {
            Log::info("processSantriActivation: User santri dengan email {$emailCalonSantri} sudah ada untuk Pendaftar ID: {$record->id}.");
            // Jika user sudah ada, pastikan status pendaftar adalah AKTIF dan hapus pendaftar.
            if ($record->status_pendaftaran !== StatusPendaftaranSantri::AKTIF) {
                $record->status_pendaftaran = StatusPendaftaranSantri::AKTIF;
                $record->saveQuietly(); // Simpan status baru jika belum
            }
            // $record->delete(); // Hapus pendaftar karena user sudah ada
            if ($sendNotification) {
                Notification::make()->title('Info')->body('Santri ini sudah memiliki akun aktif. Data pendaftar telah diperbarui/dihapus.')->warning()->send();
            }
            return;
        }
        
        // Jika user belum ada, lanjutkan proses pembuatan user baru
        if ($record->status_pendaftaran === StatusPendaftaranSantri::DITOLAK) {
            if ($sendNotification) { Notification::make()->title('Info')->body('Pendaftaran ini sudah ditolak.')->warning()->send(); }
            return;
        }

        $passwordDefault = Str::random(8); 

        try {
            DB::beginTransaction(); 

            $user = User::create([
                'name' => $record->nama_lengkap_calon_santri,
                'email' => $emailCalonSantri,
                'password' => Hash::make($passwordDefault),
                'role' => UserRole::SANTRI,
                'email_verified_at' => now(), 
            ]);

            SantriProfile::create([
                'user_id' => $user->id,
                'alamat' => $record->alamat_calon_santri,
                'tanggal_lahir' => $record->tanggal_lahir_calon_santri,
                'nama_wali' => $record->nama_wali,
            ]);

            // Jika dipanggil dari tombol 'aktivasiSantri', statusnya mungkin belum AKTIF
            if (!$fromEditAction) {
                $record->status_pendaftaran = StatusPendaftaranSantri::AKTIF;
                $record->saveQuietly(); 
            }
            // Jika dipanggil dari EditAction, $record sudah memiliki status AKTIF.

            if (!empty($record->email_wali)) {
                try {
                    Mail::to($record->email_wali)->send(new SantriBaruDiterimaMail($user, $passwordDefault, $record));
                    if ($sendNotification) { Notification::make()->title('Email Terkirim')->body('Email notifikasi telah dikirim ke wali santri.')->success()->send(); }
                } catch (\Exception $e) {
                    if ($sendNotification) { Notification::make()->title('Gagal Kirim Email')->body('Gagal mengirim email: ' . $e->getMessage())->danger()->send(); }
                    Log::error('Gagal kirim email aktivasi santri ke ' . $record->email_wali . ': ' . $e->getMessage(), ['pendaftar_id' => $record->id, 'exception' => $e]);
                }
            } else {
                if ($sendNotification) { Notification::make()->title('Info')->body('Email wali tidak tersedia, notifikasi email tidak dikirim.')->warning()->send(); }
            }

            // Hapus record PendaftarSantri setelah semua proses berhasil
            // $record->delete();
            Log::info("Pendaftar ID {$record->id} dihapus setelah aktivasi berhasil.");

            if ($sendNotification) {
                Notification::make()->title('Sukses Aktivasi')->body('Santri berhasil diaktifkan. Akun pengguna telah dibuat. Password default: ' . $passwordDefault)->success()->send();
            }
            DB::commit(); 
        } catch (\Exception $e) {
            DB::rollBack(); 
            if ($sendNotification) { Notification::make()->title('Gagal Aktivasi')->body('Terjadi kesalahan: ' . $e->getMessage())->danger()->send(); }
            Log::error('Gagal aktivasi pendaftar santri ID ' . $record->id . ': ' . $e->getMessage(), ['pendaftar_id' => $record->id, 'exception' => $e, 'trace' => $e->getTraceAsString()]);
        }
    }

    protected static function generateTempEmail(PendaftarSantri $record): string
    {
        $baseEmail = strtolower(Str::slug($record->nama_lengkap_calon_santri, '.'));
        $email = $baseEmail . '.' . Str::random(4) . '@santri.tpqanda.com'; // Ganti domain jika perlu
        while (User::where('email', $email)->exists()) {
            $email = $baseEmail . '.' . Str::random(5) . '@santri.tpqanda.com';
        }
        return $email;
    }
}