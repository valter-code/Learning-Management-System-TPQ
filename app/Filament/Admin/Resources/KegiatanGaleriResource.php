<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\KegiatanGaleriResource\Pages;
use App\Filament\Admin\Resources\KegiatanGaleriResource\RelationManagers;
use App\Models\KegiatanGaleri;
use App\Enums\UserRole; // Untuk otorisasi
use App\Enums\StatusPublikasi; // Import StatusPublikasi Enum
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth; // Auth akan digunakan di Page Class
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload; // Jika pakai media library untuk sampul

class KegiatanGaleriResource extends Resource
{
    protected static ?string $model = KegiatanGaleri::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?string $pluralModelLabel = 'Kegiatan Galeri';
    protected static ?string $modelLabel = 'Kegiatan Galeri';
    protected static ?string $slug = 'kegiatan-galeri'; // Slug URL
    protected static ?string $recordTitleAttribute = 'nama_kegiatan';
    protected static ?int $navigationSort = 4; // Paling atas di grupnya


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Kegiatan Galeri')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('nama_kegiatan')
                            ->required()->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug_kegiatan', Str::slug($state ?? '')))
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slug_kegiatan')
                            ->label('Slug (URL Otomatis)')
                            ->required()->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('deskripsi_kegiatan')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('foto_sampul')
                            ->label('Foto Sampul Galeri (Opsional)')
                            ->image()->disk('public')->directory('galeri/sampul')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Publikasi')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publikasikan Kegiatan Galeri Ini')
                            ->default(false)
                            ->reactive(),
                        Forms\Components\DateTimePicker::make('tanggal_publikasi')
                            ->label('Tanggal Publikasi (Opsional)')
                            ->helperText('Kosongkan jika ingin terbit segera saat "Publikasikan" diaktifkan. Jika diisi, akan terbit pada tanggal tersebut.')
                            ->native(false)->seconds(false)
                            ->displayFormat('d/m/Y H:i'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto_sampul')->disk('public')->label('Sampul')->circular()->defaultImageUrl(url('/images/placeholder.png')),
                Tables\Columns\TextColumn::make('nama_kegiatan')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Dibuat Oleh')->sortable()->placeholder('N/A'),
                Tables\Columns\IconColumn::make('is_published')->label('Terbit')->boolean(),
                Tables\Columns\TextColumn::make('tanggal_publikasi')->dateTime('d M Y H:i')->sortable()->label('Tgl Publikasi')->placeholder('Belum diatur'),
                Tables\Columns\TextColumn::make('fotos_count')->counts('fotos')->label('Jml Foto')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->trueLabel('Sudah Terbit')
                    ->falseLabel('Belum Terbit/Draft')
                    ->native(false),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }
    
    // Metode mutateFormDataBeforeCreate dan mutateFormDataBeforeSave DIHAPUS dari sini
    // dan akan dipindahkan ke kelas Page masing-masing.

    public static function getRelations(): array
    {
        return [
            RelationManagers\FotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKegiatanGaleris::route('/'),
            'create' => Pages\CreateKegiatanGaleri::route('/create'),
            'edit' => Pages\EditKegiatanGaleri::route('/{record}/edit'),
        ];
    }
    
    public static function canViewAny(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canCreate(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canEdit(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canDelete(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }

}
