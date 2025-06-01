<?php

namespace App\Filament\Pengajar\Resources;

use App\Filament\Pengajar\Resources\RiwayatAbsensiResource\Pages;
use App\Models\AbsensiSantri;
use App\Models\User; // Untuk relasi santri & pengajar, dan untuk mengambil santri dari kelas
use App\Enums\StatusAbsensi;
use App\Enums\UserRole;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class RiwayatAbsensiResource extends Resource
{
    protected static ?string $model = AbsensiSantri::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Riwayat Absensi Santri';
    protected static ?string $modelLabel = 'Riwayat Absensi';
    protected static ?string $pluralModelLabel = 'Riwayat Absensi';
    protected static ?string $slug = 'riwayat-absensi-santri';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        Log::info('[RiwayatAbsensiResource] getEloquentQuery - User ID: ' . ($user ? $user->id : 'Guest') . ', Role: ' . ($user && $user->role ? $user->role->value : 'N/A'));

        if ($user && $user->role === UserRole::PENGAJAR) {
            if (method_exists($user, 'mengajarDiKelas')) {
                // Dapatkan semua ID santri dari semua kelas yang diajar oleh pengajar ini
                $santriIds = User::query()
                    ->where('role', UserRole::SANTRI) // Hanya ambil santri
                    ->whereHas('kelasYangDiikuti', function (Builder $queryKelas) use ($user) {
                        // Filter kelasYangDiikuti berdasarkan kelas yang diajar oleh pengajar saat ini
                        $queryKelas->whereIn('kelas.id', $user->mengajarDiKelas()->pluck('kelas.id')->all());
                    })
                    ->pluck('users.id') // Ambil ID santri
                    ->all();

                Log::info('[RiwayatAbsensiResource] Pengajar (ID: '.$user->id.') akan melihat absensi dari santri dengan IDs:', $santriIds);
                
                if (!empty($santriIds)) {
                    // Filter record absensi_santri berdasarkan santri_id
                    $query->whereIn('santri_id', $santriIds);
                    Log::info('[RiwayatAbsensiResource] Query difilter dengan santri_id IN (' . implode(',', $santriIds) . ')');
                } else {
                    Log::info('[RiwayatAbsensiResource] Pengajar tidak memiliki santri di kelas yang diajar, atau tidak ada santri sama sekali. Query akan menghasilkan kosong.');
                    $query->whereRaw('1=0'); // Tidak ada santri yang relevan, tampilkan kosong
                }
            } else {
                Log::warning('[RiwayatAbsensiResource] Model User Pengajar tidak memiliki metode mengajarDiKelas(). Query akan menghasilkan kosong untuk Pengajar.');
                $query->whereRaw('1=0');
            }
        }
        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_absensi')->date('d M Y')->sortable()->label('Tanggal'),
                Tables\Columns\TextColumn::make('santri.name')->label('Nama Santri')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->badge()
                    ->label('Status Kehadiran')
                    ->formatStateUsing(fn ($state) => $state instanceof StatusAbsensi ? $state->getLabel() : $state)
                    ->color(fn ($state) => $state instanceof StatusAbsensi ? $state->getColor() : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')->limit(30)->tooltip(fn ($state) => $state)->placeholder('Tidak ada.'),
                Tables\Columns\TextColumn::make('created_at')
                ->label('Waktu Dicatat')
                ->dateTime('H:i:s') // Hanya waktu
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Filter::make('tanggal_absensi_rentang')
                    ->form([
                        DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal')
                            ->native(false),
                        DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_absensi', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_absensi', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['dari_tanggal'] && ! $data['sampai_tanggal']) {
                            return null;
                        }
                        if ($data['dari_tanggal'] && $data['sampai_tanggal']) {
                            return 'Tanggal: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');
                        }
                        if ($data['dari_tanggal']) {
                            return 'Dari: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y');
                        }
                        return 'Sampai: ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');
                    }),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make()->iconButton(),
            ])
            ->bulkActions([])
            ->defaultSort('tanggal_absensi', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatAbsensis::route('/'),
        ];
    }

    // Otorisasi
    public static function canViewAny(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK) || $user->role === UserRole::PENGAJAR; }
    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }
}
