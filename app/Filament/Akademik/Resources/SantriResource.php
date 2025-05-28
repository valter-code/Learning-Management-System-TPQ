<?php

namespace App\Filament\Akademik\Resources;

use Filament\Forms;
// use App\Filament\Akademik\Resources\SantriResource\RelationManagers; // Bisa ditambahkan nanti
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Kelas; // Import model Kelas
use App\Enums\UserRole; // Import UserRole Enum
use App\Filament\Akademik\Resources\SantriResource\Pages;
use Filament\Forms\Components\Section; // Untuk grouping form
use Illuminate\Support\Facades\Hash; // Untuk hashing password
use Illuminate\Database\Eloquent\SoftDeletingScope; // Jika Anda menggunakan soft delete

class SantriResource extends Resource
{
    protected static ?string $model = User::class; 

    protected static ?string $navigationLabel = 'Manajemen Santri'; 
    protected static ?string $pluralModelLabel = 'Santri'; 
    protected static ?string $slug = 'Santri'; 
    protected static ?string $modelLabel = 'Santri'; 
    protected static ?string $navigationIcon = 'heroicon-o-users'; 
    protected static ?string $navigationGroup = 'Manajemen Pengguna'; 
    protected static ?int $navigationSort = 1; 

    protected static ?string $navigationBadgeTooltip = 'Jumlah Santri';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('role', UserRole::SANTRI)->count();
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', UserRole::SANTRI);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Akun Santri')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap Santri')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true), // Unik, tapi abaikan record saat ini (untuk edit)
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create') // Wajib hanya saat create
                            ->dehydrated(fn ($state) => filled($state)) // Hanya kirim jika diisi (untuk edit)
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)) // Hash password
                            ->helperText('Kosongkan jika tidak ingin mengubah password saat edit.'),
                        Forms\Components\FileUpload::make('avatar_url')
                            ->label('Foto Profil')
                            ->image()
                            ->disk('public') // Sesuaikan disk Anda
                            ->directory('avatars/santri')
                            ->columnSpanFull(),
                        // Role diset otomatis, tidak perlu input manual dari user akademik
                        Forms\Components\Hidden::make('role')->default(UserRole::SANTRI->value),
                    ]),

                Section::make('Profil Detail Santri')
                    ->relationship('santriProfile') // Menggunakan relasi HasOne 'santriProfile' di model User
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir'),
                        Forms\Components\TextInput::make('nama_wali')
                            ->label('Nama Wali Santri')
                            ->maxLength(255),
                    ]),
                
                Section::make('Kelas yang Diikuti')
                    ->schema([
                        Forms\Components\Select::make('kelasYangDiikuti') // Nama relasi BelongsToMany di model User
                            ->label('Pilih Kelas')
                            ->multiple()
                            ->relationship(
                                name: 'kelasYangDiikuti', // Nama relasi
                                titleAttribute: 'nama_kelas' // Kolom yang ditampilkan dari model Kelas
                            )
                            ->preload()
                            ->searchable()
                            ->helperText('Pilih satu atau lebih kelas untuk santri ini.'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')->label('Foto')->circular()->defaultImageUrl(fn(User $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Santri')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('santriProfile.nama_wali') // Mengambil dari relasi
                    ->label('Nama Wali')
                    ->searchable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('kelasYangDiikuti.nama_kelas') // Menampilkan nama kelas
                    ->label('Kelas Diikuti')
                    ->badge() // Tampilkan sebagai badge jika banyak
                    ->listWithLineBreaks() // Jika ingin setiap kelas di baris baru
                    ->limitList(2) // Batasi jumlah kelas yang ditampilkan langsung
                    ->expandableLimitedList(), // Sisanya bisa dilihat dengan klik
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tambahkan filter jika perlu
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

    // Metode ini penting untuk memastikan 'role' diset ke 'santri' saat membuat record baru
    // dan juga untuk menangani penyimpanan ke santriProfile jika tidak menggunakan ->relationship pada Section
    // Namun, dengan ->relationship('santriProfile') pada Section, Filament v3 seharusnya sudah pintar.
    // Kita tambahkan ini untuk memastikan role.
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = UserRole::SANTRI->value; // Pastikan peran adalah santri
        return $data;
    }
    
    // Untuk memastikan 'role' tidak bisa diubah saat edit oleh form ini
    // public static function mutateFormDataBeforeSave(array $data): array
    // {
    //     unset($data['role']); // Jangan biarkan field role diubah dari form ini
    //     return $data;
    // }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\KelasRelationManager::class, // Jika Anda ingin relation manager untuk kelas
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSantris::route('/'), // Filament akan mengganti 'Santris' jadi 'Santri'
            'create' => Pages\CreateSantri::route('/create'),
            'edit' => Pages\EditSantri::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canCreate(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canEdit(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canDelete(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
}