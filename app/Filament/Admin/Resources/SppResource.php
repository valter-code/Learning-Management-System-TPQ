<?php

namespace App\Filament\Admin\Resources; // Atau panel yang sesuai

use Carbon\Carbon;
use App\Models\Spp;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Enums\UserRole;
use Filament\Infolists;
use App\Enums\StatusSpp;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Admin\Resources\SppResource\Pages;
use App\Mail\TagihanSppMail; // Pastikan Mailable ini ada dan benar
use Illuminate\Database\Eloquent\Collection; // Untuk BulkAction
use App\Models\Setting; // Import Setting model

class SppResource extends Resource
{
    protected static ?string $model = Spp::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Manajemen SPP Santri';
    protected static ?string $slug = 'spp';

    protected static ?string $modelLabel = 'Data SPP';
    protected static ?string $pluralModelLabel = 'Data SPP Santri';
    protected static ?string $navigationGroup = 'Keuangan';

    public static function form(Form $form): Form
    {
        // Ambil nilai default SPP dari settings untuk form
        $defaultSppAmount = (float)(Setting::where('key', 'sistem.jumlah_spp_default')->first()?->value ?? config('sistem.jumlah_spp_default', 150000));

        return $form
            ->schema([
                Forms\Components\Select::make('santri_id')
                    ->relationship('santri', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('role', UserRole::SANTRI))
                    ->searchable()->preload()->required()->label('Nama Santri'),
                Forms\Components\Select::make('bulan')
                    ->options(array_combine(range(1,12), array_map(fn($m) => Carbon::create()->month($m)->translatedFormat('F'), range(1,12))))
                    ->required()->native(false)->default(now()->month),
                Forms\Components\TextInput::make('tahun')->numeric()->required()->minValue(2020)->maxValue(date('Y') + 5)->default(date('Y')),
                
                Forms\Components\TextInput::make('biaya_bulanan')
                    ->label('Biaya SPP Seharusnya')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->default($defaultSppAmount) 
                    ->helperText('Biaya SPP yang ditetapkan untuk bulan ini.'),
                
                Forms\Components\TextInput::make('jumlah_bayar')
                    ->label('Jumlah Tagihan/Akan Dibayar (Bulan Ini)') // Label diperjelas
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->default($defaultSppAmount) 
                    ->helperText('Jumlah yang akan ditagihkan untuk SPP bulan ini. Biasanya sama dengan Biaya SPP Seharusnya.'), // Helper diperjelas
                
                Forms\Components\DatePicker::make('tanggal_bayar')->label('Tanggal Pembayaran Aktual')->native(false),
                Forms\Components\Select::make('status_pembayaran')->options(StatusSpp::class)
                    ->required()->default(StatusSpp::BELUM_BAYAR->value)->native(false)->live(),
                Forms\Components\Textarea::make('catatan')->columnSpanFull(),
                // pencatat_id akan diisi otomatis
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('santri.name')->label('Nama Santri')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama_bulan')->label('Bulan SPP')->sortable(),
                Tables\Columns\TextColumn::make('tahun')->sortable(),

                Tables\Columns\TextColumn::make('biaya_bulanan')
                    ->money('IDR')
                    ->label('Biaya bulanan')
                    ->sortable(),

                // --- PERUBAHAN DI SINI untuk Jml. Tagihan Kumulatif ---
                Tables\Columns\TextColumn::make('jumlah_tunggakan_kumulatif') // Menggunakan nama baru untuk state, atau bisa tetap 'jumlah_bayar' jika tidak ada field 'jumlah_tunggakan_kumulatif' di model
                    ->label('Jml. Tagihan Kumulatif')
                    ->money('IDR')
                    ->state(function (Spp $record): float {
                        // Hitung total tunggakan untuk santri ini SAMPAI DENGAN bulan & tahun record saat ini
                        $totalTunggakan = Spp::where('santri_id', $record->santri_id)
                            ->whereIn('status_pembayaran', [StatusSpp::BELUM_BAYAR->value, StatusSpp::TERLAMBAT->value])
                            ->where(function ($query) use ($record) {
                                $query->where('tahun', '<', $record->tahun)
                                      ->orWhere(function ($query) use ($record) {
                                          $query->where('tahun', $record->tahun)
                                                ->where('bulan', '<=', $record->bulan);
                                      });
                            })
                            ->sum('biaya_bulanan'); // atau ->sum('jumlah_bayar') jika itu yang dimaksud sebagai nominal per bulan
                        return (float) $totalTunggakan;
                    })
                    ->sortable(), // Sorting berdasarkan nilai kalkulasi ini mungkin berat
                // --- AKHIR PERUBAHAN ---

                // Jika Anda masih ingin menampilkan tagihan spesifik bulan itu saja, Anda bisa buat kolom lain:
                // Tables\Columns\TextColumn::make('jumlah_bayar')
                //     ->money('IDR')
                //     ->label('Tagihan Bulan Ini') // Label lebih spesifik
                //     ->sortable(),

                Tables\Columns\TextColumn::make('status_pembayaran')->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof StatusSpp ? $state->getLabel() : $state)
                    ->color(fn ($state) => $state instanceof StatusSpp ? $state->getColor() : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_bayar')->date('d M Y')->label('Tgl Bayar')->placeholder('Belum ada')->sortable(),
                Tables\Columns\TextColumn::make('pencatat.name')->label('Dicatat Oleh')->placeholder('N/A')->sortable(),
            ])
            // ... sisa filter, action, dll.
            ->filters([
                Tables\Filters\SelectFilter::make('status_pembayaran')->options(StatusSpp::class)->native(false),
                Tables\Filters\SelectFilter::make('santri_id')
                    ->relationship('santri', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('role', UserRole::SANTRI))
                    ->searchable()->preload()->native(false)->label('Santri'),
                Tables\Filters\SelectFilter::make('bulan')
                    ->options(array_combine(range(1,12), array_map(fn($m) => Carbon::create()->month($m)->translatedFormat('F'), range(1,12))))
                    ->native(false),
                Tables\Filters\SelectFilter::make('tahun')
                    ->options(array_combine(range(date('Y') - 3, date('Y') + 1), range(date('Y') - 3, date('Y') + 1)))
                    ->default(date('Y'))->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                     ->mutateFormDataUsing(function (array $data, Spp $record): array {
                        $data['pencatat_id'] = Auth::id();
                        $newStatus = $data['status_pembayaran'] instanceof StatusSpp ? $data['status_pembayaran'] : StatusSpp::tryFrom((string)$data['status_pembayaran']);
                        
                        if ($newStatus === StatusSpp::SUDAH_BAYAR && empty($data['tanggal_bayar'])) {
                            if ($record->status_pembayaran !== StatusSpp::SUDAH_BAYAR || is_null($record->tanggal_bayar)) {
                                $data['tanggal_bayar'] = now();
                            } else {
                                $data['tanggal_bayar'] = $record->tanggal_bayar;
                            }
                        }
                        elseif ($record->status_pembayaran === StatusSpp::SUDAH_BAYAR && $newStatus !== StatusSpp::SUDAH_BAYAR) {
                            $data['tanggal_bayar'] = null;
                        }
                        return $data;
                    }),
                Tables\Actions\Action::make('tagihSpp')
                    // ... (kode tagihSpp tidak diubah) ...
                    ->label('Tagih SPP')
                    ->icon('heroicon-o-envelope')->color('warning')->requiresConfirmation()
                    ->modalHeading('Kirim Tagihan SPP?')
                    ->modalDescription(function(Spp $record): HtmlString {
                        $emailWali = $record->santri?->santriProfile?->email_wali ?? $record->santri?->email ?? 'Tidak ditemukan';
                        // Ambil total tunggakan untuk deskripsi modal
                        $totalTunggakan = Spp::where('santri_id', $record->santri_id)
                            ->whereIn('status_pembayaran', [StatusSpp::BELUM_BAYAR->value, StatusSpp::TERLAMBAT->value])
                            ->where(function ($query) use ($record) { // Bisa disederhanakan jika hanya tagihan bulan ini
                                $query->where('tahun', '<', $record->tahun)
                                      ->orWhere(function ($query) use ($record) {
                                          $query->where('tahun', $record->tahun)
                                                ->where('bulan', '<=', $record->bulan);
                                      });
                            })
                            ->sum('biaya_bulanan'); // atau jumlah_bayar
                        
                        $tunggakanFormatted = 'Rp ' . number_format($totalTunggakan, 0, ',', '.');
                        if ($record->status_pembayaran === StatusSpp::BELUM_BAYAR || $record->status_pembayaran === StatusSpp::TERLAMBAT) {
                             return new HtmlString(
                                'Ini akan mengirim email tagihan SPP bulan ' . '<strong>' . $record->nama_bulan . ' ' . $record->tahun . '</strong>' .
                                ' (Rp ' . number_format($record->biaya_bulanan,0,',','.') . ')' . // Tagihan bulan ini
                                ' untuk santri ' . '<strong>' .  $record->santri->name . '</strong>' .
                                '. Total tunggakan saat ini: <strong>' . $tunggakanFormatted . '</strong>' .
                                ' ke email wali: <strong>' . $emailWali . '</strong>.'
                            );
                        }
                        // Fallback jika status bukan belum bayar/terlambat (seharusnya tidak visible)
                        return new HtmlString(
                            'Tagihan SPP untuk santri ' . '<strong>' .  $record->santri->name . '</strong>' .
                            ' ke email wali: <strong>' . $emailWali . '</strong>.'
                        );
                    })
                    ->action(function (Spp $record) {
                        // ... (logika action tidak berubah) ...
                        Log::info("Mencoba menagih SPP untuk record ID: {$record->id}, Santri: {$record->santri->name}");
                        $emailWali = $record->santri?->santriProfile?->email_wali ?? $record->santri?->email;
                        Log::info("Email wali yang ditemukan untuk tagihan: " . ($emailWali ?? 'TIDAK ADA'));

                        if (empty($emailWali)) {
                            Notification::make()->danger()->title('Gagal Kirim')->body('Email wali untuk santri ' . $record->santri->name . ' tidak ditemukan.')->send();
                            return;
                        }
                        try {
                            // Anda mungkin ingin mengirim total tunggakan juga di email
                            // $totalTunggakan = Spp::where('santri_id', $record->santri_id) ... (kalkulasi seperti di atas)
                            // Mail::to($emailWali)->send(new TagihanSppMail($record, $totalTunggakan)); // Jika TagihanSppMail diupdate
                            Mail::to($emailWali)->send(new TagihanSppMail($record)); // Kirim record SPP saat ini
                            Notification::make()->success()->title('Tagihan Terkirim')->body('Email tagihan SPP berhasil dikirim ke ' . $emailWali)->send();
                            Log::info("Email tagihan SPP berhasil dikirim ke {$emailWali} untuk SPP ID: {$record->id}");
                        } catch (\Exception $e) {
                            Notification::make()->danger()->title('Gagal Kirim Email')->body('Terjadi kesalahan: ' . $e->getMessage())->send();
                            Log::error('Gagal kirim email tagihan SPP untuk SPP ID: ' . $record->id . '. Error: ' . $e->getMessage(), ['exception' => $e]);
                        }
                    })
                    ->visible(fn (Spp $record): bool => $record->status_pembayaran === StatusSpp::BELUM_BAYAR || $record->status_pembayaran === StatusSpp::TERLAMBAT),
            ])
            ->bulkActions([
                // ... (bulkActions tidak diubah) ...
                 Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('tagihSppMassal')
                        ->label('Tagih SPP Terpilih')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Kirim Tagihan SPP Massal?')
                        ->modalDescription('Ini akan mengirim email tagihan untuk semua SPP yang dipilih dan statusnya "Belum Bayar" atau "Terlambat". Lanjutkan?')
                        ->action(function (Collection $records) {
                            $berhasilKirim = 0;
                            $gagalKirim = 0;
                            $tidakAdaEmail = 0;

                            foreach ($records as $record) {
                                if ($record->status_pembayaran === StatusSpp::BELUM_BAYAR || $record->status_pembayaran === StatusSpp::TERLAMBAT) {
                                    $emailWali = $record->santri?->santriProfile?->email_wali ?? $record->santri?->email;
                                    if (empty($emailWali)) {
                                        $tidakAdaEmail++;
                                        Log::warning("Tagihan SPP Massal: Email wali tidak ditemukan untuk SPP ID {$record->id}, Santri: {$record->santri->name}");
                                        continue;
                                    }
                                    try {
                                        // Jika TagihanSppMail diupdate untuk menerima total tunggakan, hitung di sini.
                                        Mail::to($emailWali)->send(new TagihanSppMail($record));
                                        $berhasilKirim++;
                                        Log::info("Tagihan SPP Massal: Email berhasil dikirim ke {$emailWali} untuk SPP ID: {$record->id}");
                                    } catch (\Exception $e) {
                                        $gagalKirim++;
                                        Log::error("Tagihan SPP Massal: Gagal kirim email ke {$emailWali} untuk SPP ID: {$record->id}. Error: " . $e->getMessage());
                                    }
                                }
                            }

                            if ($berhasilKirim > 0) {
                                Notification::make()->success()->title('Tagihan Massal Terkirim')
                                    ->body("Berhasil mengirim {$berhasilKirim} email tagihan." . 
                                           ($gagalKirim > 0 ? " Gagal mengirim {$gagalKirim} email." : "") .
                                           ($tidakAdaEmail > 0 ? " {$tidakAdaEmail} santri tidak memiliki email wali." : "") )
                                    ->send();
                            } elseif ($gagalKirim > 0 || $tidakAdaEmail > 0) {
                                Notification::make()->danger()->title('Sebagian Tagihan Gagal Terkirim')
                                    ->body(($gagalKirim > 0 ? "Gagal mengirim {$gagalKirim} email. " : "") .
                                           ($tidakAdaEmail > 0 ? "{$tidakAdaEmail} santri tidak memiliki email wali. " : "") .
                                           "Periksa log untuk detail.")
                                    ->send();
                            } else {
                                 Notification::make()->info()->title('Tidak Ada Tagihan Dikirim')
                                    ->body("Tidak ada SPP terpilih yang memenuhi kriteria untuk ditagih.")
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);  
        }
    
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('santri.name')->label('Nama Santri'),
                Infolists\Components\TextEntry::make('nama_bulan')->label('Bulan SPP'),
                Infolists\Components\TextEntry::make('tahun')->label('Tahun SPP'),
                Infolists\Components\TextEntry::make('biaya_bulanan')
                    ->money('IDR')
                    ->label('Biaya SPP Seharusnya'),

                // Untuk Infolist, Anda juga bisa menampilkan total tunggakan jika relevan
                Infolists\Components\TextEntry::make('jumlah_bayar') // Ini adalah tagihan bulan ini
                    ->money('IDR')
                    ->label('Jumlah Tagihan (Bulan Ini)'), 
                
                Infolists\Components\TextEntry::make('total_tunggakan_santri')
                    ->label('Total Tunggakan Santri Saat Ini')
                    ->money('IDR')
                    ->state(function (Spp $record): float {
                        return (float) Spp::where('santri_id', $record->santri_id)
                            ->whereIn('status_pembayaran', [StatusSpp::BELUM_BAYAR->value, StatusSpp::TERLAMBAT->value])
                            ->sum('biaya_bulanan'); // atau jumlah_bayar
                    }),

                Infolists\Components\TextEntry::make('status_pembayaran')->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof StatusSpp ? $state->getLabel() : $state)
                    ->color(fn ($state) => $state instanceof StatusSpp ? $state->getColor() : 'gray'),
                Infolists\Components\TextEntry::make('tanggal_bayar')->date('d F Y')->label('Tanggal Bayar')->placeholder('Belum dibayar'),
                Infolists\Components\TextEntry::make('pencatat.name')->label('Dicatat Oleh')->placeholder('N/A'),
                Infolists\Components\TextEntry::make('catatan')->placeholder('Tidak ada catatan.'),
            ]);
    }
    // ... sisa kode resource ...
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpps::route('/'),
            'view' => Pages\ViewSpp::route('/{record}'),
            // 'edit' => Pages\EditSpp::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canCreate(): bool { return false; } 
    public static function canEdit(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canDelete(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
}