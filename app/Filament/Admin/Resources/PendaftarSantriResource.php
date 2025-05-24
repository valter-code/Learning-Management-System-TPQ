<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Enums\UserRole;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\SantriProfile;
use App\Models\PendaftarSantri;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use App\Mail\SantriBaruDiterimaMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Enums\StatusPendaftaranSantri;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Admin\Resources\PendaftarSantriResource\Pages;

class PendaftarSantriResource extends Resource
{
    protected static ?string $model = PendaftarSantri::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Pendaftar Santri Baru';
    protected static ?string $modelLabel = 'Pendaftar Santri';
    protected static ?string $pluralModelLabel = 'Pendaftar Santri';
    protected static ?string $slug = 'pendaftar-santri';

    protected static ?string $navigationGroup = 'Manajemen Pengguna'; // Atau grup lain
    protected static ?int $navigationSort = 3; // Sesuaikan urutan

    protected static ?string $navigationBadgeTooltip = 'Santri Baru';
    public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}


    public static function form(Form $form): Form // Form ini untuk edit catatan admin atau status
    {
        return $form
        ->schema([
            Forms\Components\Select::make('status_pendaftaran')
                ->label('Status Pendaftaran')
                ->options(StatusPendaftaranSantri::class) 
                ->required()
                ->native(false),
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
            Tables\Actions\EditAction::make()->label('Ubah Status/Catatan'),
            Tables\Actions\Action::make('aktivasiSantri')
                ->label('Aktifkan Santri')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Aktifkan Pendaftaran Santri?')
                ->modalDescription('Setelah diaktifkan, data pendaftar akan dipindahkan ke daftar santri aktif dan akun pengguna akan dibuat.')
                ->action(function (PendaftarSantri $record) {
                    Log::info('Aksi aktivasiSantri dimulai untuk pendaftar ID: ' . $record->id);

                    if ($record->status_pendaftaran === StatusPendaftaranSantri::AKTIF) {
                        Notification::make()->title('Info')->body('Santri ini sudah aktif.')->warning()->send();
                        Log::warning('Aktivasi dibatalkan: Santri sudah aktif.', ['pendaftar_id' => $record->id]);
                        return;
                    }
                    if ($record->status_pendaftaran === StatusPendaftaranSantri::DITOLAK) {
                        Notification::make()->title('Info')->body('Pendaftaran ini sudah ditolak dan tidak bisa diaktifkan.')->warning()->send();
                        Log::warning('Aktivasi dibatalkan: Pendaftaran ditolak.', ['pendaftar_id' => $record->id]);
                        return;
                    }

                    $passwordDefault = '12345678'; 

                    $emailSantri = $record->email_wali; 
                    if (empty($emailSantri) || User::where('email', $emailSantri)->exists()) {
                        $baseEmail = strtolower(Str::slug($record->nama_lengkap_calon_santri, '.'));
                        $emailSantri = $baseEmail . '.' . Str::random(4) . '@santri.tpqanda.com'; // Ganti domain
                        // Pastikan email ini benar-benar unik
                        while (User::where('email', $emailSantri)->exists()) {
                            $emailSantri = $baseEmail . '.' . Str::random(5) . '@santri.tpqanda.com';
                        }
                        Log::info('Email wali kosong atau sudah ada, email santri baru dibuat: ' . $emailSantri, ['pendaftar_id' => $record->id]);
                    }

                    Log::info('Membuat user baru untuk santri.', ['nama' => $record->nama_lengkap_calon_santri, 'email' => $emailSantri, 'pendaftar_id' => $record->id]);
                    $user = User::create([
                        'name' => $record->nama_lengkap_calon_santri,
                        'email' => $emailSantri,
                        'password' => Hash::make($passwordDefault),
                        'role' => UserRole::SANTRI,
                        'email_verified_at' => now(),
                    ]);
                    Log::info('User baru berhasil dibuat dengan ID: ' . $user->id, ['pendaftar_id' => $record->id]);

                    Log::info('Membuat SantriProfile untuk user ID: ' . $user->id, ['pendaftar_id' => $record->id]);
                    SantriProfile::create([
                        'user_id' => $user->id,
                        'alamat' => $record->alamat_calon_santri,
                        'tanggal_lahir' => $record->tanggal_lahir_calon_santri,
                        'nama_wali' => $record->nama_wali,
                    ]);
                    Log::info('SantriProfile berhasil dibuat.', ['pendaftar_id' => $record->id]);
                    
                    if (!empty($record->email_wali)) {
                        Log::info('Mencoba mengirim email ke wali: ' . $record->email_wali, ['pendaftar_id' => $record->id]);
                        try {
                            Mail::to($record->email_wali)->send(new SantriBaruDiterimaMail($user, $passwordDefault, $record));
                            Notification::make()->title('Email Terkirim')->body('Email notifikasi telah dikirim ke wali santri.')->success()->send();
                            Log::info('Email berhasil dikirim ke: ' . $record->email_wali, ['pendaftar_id' => $record->id]);
                        } catch (\Exception $e) {
                            Notification::make()->title('Gagal Kirim Email')->body('Terjadi kesalahan saat mengirim email: Periksa konfigurasi SMTP dan log error.')->danger()->send();
                            Log::error('Gagal kirim email aktivasi santri ke ' . $record->email_wali . ': ' . $e->getMessage(), [
                                'pendaftar_id' => $record->id,
                                'exception' => $e
                            ]);
                        }
                    } else {
                        Notification::make()->title('Info')->body('Email wali tidak tersedia, notifikasi email tidak dikirim.')->warning()->send();
                        Log::warning('Email wali tidak tersedia, email tidak dikirim.', ['pendaftar_id' => $record->id]);
                    }

                    Log::info('Menghapus data pendaftar ID: ' . $record->id);
                    $record->delete(); 

                    Notification::make()->title('Sukses')->body('Santri berhasil diaktifkan. Akun pengguna telah dibuat. Password default: ' . $passwordDefault)->success()->send();
                    Log::info('Aktivasi santri berhasil untuk pendaftar ID: ' . $record->id . ', User ID baru: ' . $user->id);
                })
                ->visible(fn (PendaftarSantri $record) => $record->status_pendaftaran === StatusPendaftaranSantri::PENDING),
        ])
        ->bulkActions([ /* ... */ ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pendaftar')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('nama_lengkap_calon_santri'),
                        Infolists\Components\TextEntry::make('tempat_lahir_calon_santri')->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('tanggal_lahir_calon_santri')->date('d F Y'),
                        Infolists\Components\TextEntry::make('jenis_kelamin_calon_santri')->formatStateUsing(fn(string $state) => ucfirst($state)),
                        Infolists\Components\TextEntry::make('alamat_calon_santri')->columnSpanFull()->placeholder('N/A'),
                    ]),
                Infolists\Components\Section::make('Informasi Wali')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('nama_wali'),
                        Infolists\Components\TextEntry::make('nomor_telepon_wali'),
                        Infolists\Components\TextEntry::make('email_wali')->placeholder('N/A'),
                        Infolists\Components\TextEntry::make('pekerjaan_wali')->placeholder('N/A'),
                    ]),
                Infolists\Components\Section::make('Status Pendaftaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('status_pendaftaran')
                            ->badge()
                            ->formatStateUsing(fn (StatusPendaftaranSantri $state) => $state->getLabel())
                            ->color(fn (StatusPendaftaranSantri $state) => $state->getColor()),
                        Infolists\Components\TextEntry::make('catatan_admin')->placeholder('Tidak ada catatan.'),
                        Infolists\Components\TextEntry::make('created_at')->label('Tanggal Mendaftar')->dateTime('d F Y, H:i'),
                    ]),
            ]);
    }
    
    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendaftarSantris::route('/'),
            'create' => Pages\CreatePendaftarSantri::route('/create'), // Admin/Akademik bisa input manual jika perlu
            // 'edit' => Pages\EditPendaftarSantri::route('/{record}/edit'), // Untuk ubah status/catatan
            'view' => Pages\ViewPendaftarSantri::route('/{record}'), // Untuk lihat detail
        ];
    }
    
    // Otorisasi untuk Admin & Akademik
    public static function canViewAny(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    // ... (tambahkan canCreate, canEdit, canDelete jika Admin/Akademik boleh CRUD pendaftar)
}
