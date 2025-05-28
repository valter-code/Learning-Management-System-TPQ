<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\RiwayatAbsensiPengajarResource\Pages;
use App\Models\AbsenPengajar; 
use App\Models\User; 
use App\Enums\StatusAbsensi; 
use App\Enums\UserRole; 
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter; 
use Filament\Forms\Components\DatePicker; 
use Illuminate\Database\Eloquent\Model;

class RiwayatAbsensiPengajarResource extends Resource
{
    protected static ?string $model = AbsenPengajar::class; 

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Riwayat Absensi Pengajar';
    protected static ?string $modelLabel = 'Riwayat Absensi Pengajar';
    protected static ?string $pluralModelLabel = 'Riwayat Absensi Pengajar';
    protected static ?string $slug = 'riwayat-absensi-pengajar';

    protected static ?string $navigationGroup = 'Laporan'; 
    protected static ?int $navigationSort = 2; 

    // Admin bisa melihat semua riwayat absensi pengajar
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['pengajar']); 
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal') 
                    ->date('d M Y')
                    ->label('Tanggal Absensi')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pengajar.name') 
                    ->label('Nama Pengajar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status') 
                    ->label('Status Kehadiran')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof StatusAbsensi ? $state->getLabel() : $state)
                    ->color(fn ($state) => $state instanceof StatusAbsensi ? $state->getColor() : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state)
                    ->placeholder('Tidak ada keterangan.'),
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
                                // Sesuaikan 'tanggal' dengan nama kolom tanggal di tabel Anda
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                // Sesuaikan 'tanggal' dengan nama kolom tanggal di tabel Anda
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['dari_tanggal'] && ! $data['sampai_tanggal']) {return null;}
                        if ($data['dari_tanggal'] && $data['sampai_tanggal']) {return 'Tanggal: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');}
                        if ($data['dari_tanggal']) {return 'Dari: ' . \Carbon\Carbon::parse($data['dari_tanggal'])->format('d/m/Y');}
                        return 'Sampai: ' . \Carbon\Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');
                    }),
                SelectFilter::make('pengajar_id') // Filter berdasarkan pengajar
                    ->label('Filter Pengajar')
                    ->relationship('pengajar', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('role', UserRole::PENGAJAR))
                    ->searchable()
                    ->preload()
                    ->native(false),
                SelectFilter::make('status') // Filter berdasarkan status absensi
                    ->label('Filter Status')
                    ->options(StatusAbsensi::class) // Otomatis dari Enum
                    ->native(false),
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc'); // Urutkan berdasarkan tanggal terbaru
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRiwayatAbsensiPengajars::route('/'),
            // 'create' => Pages\CreateRiwayatAbsensiPengajar::route('/create'), // Admin tidak create dari sini
            // 'edit' => Pages\EditRiwayatAbsensiPengajar::route('/{record}/edit'),
            // 'view' => Pages\ViewRiwayatAbsensiPengajar::route('/{record}'), // Jika ingin halaman detail
        ];
    }

    // Otorisasi: Hanya Admin yang bisa melihat menu dan data ini
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && $user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK;
    }
    // Admin tidak create/edit/delete dari sini, hanya view

    // public static function canViewAny(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
//     public static function canCreate(): bool { return false; } // Diubah ke false
//     public static function canEdit(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
//     public static function canDelete(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
}
