<?php

namespace App\Filament\Santri\Resources;

use App\Filament\Santri\Resources\RiwayatAbsensiSantriResource\Pages;
use App\Models\AbsensiSantri;
use App\Models\User;
use App\Enums\StatusAbsensi;
use App\Enums\UserRole;
use Filament\Forms; 
use Filament\Forms\Form; 
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Infolist; 
use Filament\Infolists\Components; 

class RiwayatAbsensiSantriResource extends Resource
{
    protected static ?string $model = AbsensiSantri::class;

    protected static ?string $navigationLabel = 'Riwayat Absensi Saya';
    protected static ?string $modelLabel = 'Riwayat Absensi';
    protected static ?string $pluralModelLabel = 'Riwayat Absensi Saya'; 
    protected static ?string $slug = 'riwayat-absensi-saya';
    protected static ?int $navigationSort = 4; 
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Laporan';


    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('santri_id', Auth::id());
    }

    // Form tidak diperlukan karena view-only dan tidak ada create/edit dari sini
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_absensi')
                    ->date('d F Y') // Format lebih lengkap
                    ->label('Tanggal')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof StatusAbsensi ? $state->getLabel() : $state)
                    ->color(fn ($state) => $state instanceof StatusAbsensi ? $state->getColor() : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state)
                    ->placeholder('Tidak ada keterangan.')
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Absen')
                    ->dateTime('H:i:s') // Hanya waktu
                    ->sortable(),
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
                        if (! $data['dari_tanggal'] && ! $data['sampai_tanggal']) {return null;}
                        if ($data['dari_tanggal'] && $data['sampai_tanggal']) {return 'Tanggal: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');}
                        if ($data['dari_tanggal']) {return 'Dari: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y');}
                        return 'Sampai: ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton(), // Santri bisa lihat detail jika ada Infolist
            ])
            ->bulkActions([]) // Tidak ada bulk action untuk santri
            ->defaultSort('tanggal_absensi', 'desc');
    }

    // Infolist untuk halaman view detail (opsional)
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Detail Absensi')
                    ->columns(2)
                    ->schema([
                        Components\TextEntry::make('tanggal_absensi')->date('l, d F Y'),
                        Components\TextEntry::make('status_kehadiran')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state instanceof StatusAbsensi ? $state->getLabel() : $state)
                            ->color(fn ($state) => $state instanceof StatusAbsensi ? $state->getColor() : 'gray'),
                        Components\TextEntry::make('keterangan')->columnSpanFull()->placeholder('Tidak ada keterangan.'),
                        Components\TextEntry::make('pengajar.name')->label('Dicatat Oleh')->placeholder('Mandiri'),
                        Components\TextEntry::make('created_at')->label('Waktu Absen')->dateTime('H:i:s'),
                        Components\TextEntry::make('updated_at')->label('Terakhir Diperbarui')->dateTime('d M Y H:i:s')->columnSpanFull(),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Tidak ada relasi yang perlu dimanage dari sini oleh santri
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatAbsensiSantris::route('/'), // Halaman daftar
            // 'view' => Pages\ViewRiwayatAbsensiSantri::route('/{record}'), // Halaman detail
            // tidak mendaftarkan 'create' dan 'edit'
        ];
    }

    // Otorisasi: Hanya santri yang bisa melihat menu ini
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && $user->role === UserRole::SANTRI;
    }
    
    // Santri tidak bisa membuat, mengedit, atau menghapus dari resource ini
    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }
    public static function canForceDelete(Model $record): bool { return false; }
    public static function canForceDeleteAny(): bool { return false; }
    public static function canRestore(Model $record): bool { return false; }
    public static function canRestoreAny(): bool { return false; }

}
