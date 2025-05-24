<?php

namespace App\Filament\Akademik\Resources;

use App\Filament\Akademik\Resources\KelasResource\Pages;
use App\Filament\Akademik\Resources\KelasResource\RelationManagers;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\UserRole;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap'; 
    protected static ?string $navigationGroup = 'Manajemen Akademik'; 
    protected static ?int $navigationSort = 2; // Paling atas di grupnya


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('nama_kelas')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('deskripsi')
                ->maxLength(255)
                ->columnSpanFull(),
            // Forms\Components\Select::make('wali_kelas_id')
            //     ->label('Wali Kelas')
            //     ->relationship('waliKelas', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('role', UserRole::PENGAJAR))
            //     ->searchable()
            //     ->preload()
            //     ->helperText('Pilih pengajar yang akan menjadi wali kelas. Hanya user dengan role Pengajar yang tampil.'),
            Forms\Components\Select::make('pengajars') // Ini untuk relasi belongsToMany 'pengajars' di model Kelas
                ->label('Daftar Pengajar Kelas')
                ->multiple()
                ->relationship('pengajars', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('role', UserRole::PENGAJAR))
                ->preload()
                ->searchable()
                ->helperText('Pilih satu atau lebih pengajar untuk kelas ini.')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('nama_kelas')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('deskripsi')
                ->limit(50)
                ->tooltip(fn (Kelas $record): ?string => $record->deskripsi)
                ->toggleable(isToggledHiddenByDefault: true),
            // HAPUS KOLOM UNTUK WALI KELAS:
            // Tables\Columns\TextColumn::make('waliKelas.name')
            //     ->label('Wali Kelas')
            //     ->searchable()
            //     ->sortable()
            //     ->placeholder('Belum ada wali kelas'),
            Tables\Columns\TextColumn::make('pengajars_count')
                ->counts('pengajars')
                ->label('Jumlah Pengajar')
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            // HAPUS FILTER UNTUK WALI KELAS JIKA ADA:
            // Tables\Filters\SelectFilter::make('wali_kelas_id')
            //     ->label('Wali Kelas')
            //     ->relationship('waliKelas', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('role', UserRole::PENGAJAR))
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
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
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}
