<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Enums\UserRole;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\StatusAbsensi;
use App\Models\AbsenPengajar;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\RiwayatAbsensiPengajar;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RiwayatAbsensiPengajarResource\Pages;
use App\Filament\Resources\RiwayatAbsensiPengajarResource\RelationManagers;



class RiwayatAbsensiPengajarResource extends Resource
{
    protected static ?string $model = AbsenPengajar::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle'; // Ikon yang lebih personal
    protected static ?string $navigationLabel = 'Riwayat Absensi Saya';
    protected static ?string $modelLabel = 'Riwayat Absensi Pribadi';
    protected static ?string $pluralModelLabel = 'Riwayat Absensi Saya';
    protected static ?string $slug = 'riwayat-absensi-pribadi-pengajar'; // Slug unik
    protected static ?string $navigationGroup = 'Laporan Saya'; // Grup navigasi baru atau yang sudah ada
    protected static ?int $navigationSort = 1; // Urutan dalam grup

    // Pengajar hanya bisa melihat riwayat absensinya sendiri
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('pengajar_id', Auth::id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal') // Sesuaikan dengan nama kolom tanggal di AbsenPengajar
                    ->date('d F Y') 
                    ->label('Tanggal')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status') // Sesuaikan dengan nama kolom status di AbsenPengajar
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof StatusAbsensi ? $state->getLabel() : $state)
                    ->color(fn ($state) => $state instanceof StatusAbsensi ? $state->getColor() : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('waktu_masuk') // Jika ada kolom waktu_masuk
                    ->label('Waktu Masuk')
                    ->time('H:i') // Format waktu
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state)
                    ->placeholder('Tidak ada keterangan.')
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Absen Dicatat')
                    ->dateTime('H:i:s') 
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                                // Sesuaikan 'tanggal' dengan nama kolom tanggal di tabel AbsenPengajar
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                // Sesuaikan 'tanggal' dengan nama kolom tanggal di tabel AbsenPengajar
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['dari_tanggal'] && ! $data['sampai_tanggal']) {return null;}
                        if ($data['dari_tanggal'] && $data['sampai_tanggal']) {return 'Tanggal: ' . Carbon::parse($data['dari_tanggal'])->format('d/m/Y') . ' - ' . Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');}
                        if ($data['dari_tanggal']) {return 'Dari: ' . Carbon::parse($data['dari_tanggal'])->format('d/m/Y');}
                        return 'Sampai: ' . Carbon::parse($data['sampai_tanggal'])->format('d/m/Y');
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton(),
            ])
            ->bulkActions([])
            ->defaultSort('tanggal', 'desc'); // Sesuaikan nama kolom tanggal
    }

    // Infolist untuk halaman view detail
    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return $infolist
    //         ->schema([
    //             Components\Section::make('Detail Absensi Saya')
    //                 ->columns(2)
    //                 ->schema([
    //                     Components\TextEntry::make('tanggal')->date('l, d F Y'), // Sesuaikan nama kolom
    //                     Components\TextEntry::make('status') // Sesuaikan nama kolom
    //                         ->badge()
    //                         ->formatStateUsing(fn ($state) => $state instanceof StatusAbsensi ? $state->getLabel() : $state)
    //                         ->color(fn ($state) => $state instanceof StatusAbsensi ? $state->getColor() : 'gray'),
    //                     Components\TextEntry::make('waktu_masuk')->time('H:i')->label('Waktu Masuk')->placeholder('-'), // Jika ada
    //                     Components\TextEntry::make('keterangan')->columnSpanFull()->placeholder('Tidak ada keterangan.'),
    //                     Components\TextEntry::make('created_at')->label('Waktu Absen Dicatat')->dateTime('d F Y, H:i:s'),
    //                     Components\TextEntry::make('updated_at')->label('Terakhir Diperbarui')->dateTime('d M Y H:i:s')->columnSpanFull(),
    //                 ])
    //         ]);
    
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
            // 'create' => Pages\CreateRiwayatAbsensiPengajar::route('/create'),
            // 'edit' => Pages\EditRiwayatAbsensiPengajar::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool { $user = Auth::user(); return $user && in_array($user->role, [UserRole::PENGAJAR, UserRole::AKADEMIK, UserRole::ADMIN]); }
    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }
}
