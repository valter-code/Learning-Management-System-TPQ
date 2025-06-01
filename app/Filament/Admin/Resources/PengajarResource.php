<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PengajarResource\Pages;
use App\Models\User;
use App\Enums\UserRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class PengajarResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Staff Pengajar';
    protected static ?string $modelLabel = 'Staff Pengajar';
    protected static ?string $pluralModelLabel = 'Staff Pengajar';
    protected static ?string $slug = 'staff-pengajar';

    protected static ?string $navigationBadgeTooltip = 'Jumlah Staff Pengajar';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('role', UserRole::PENGAJAR)->count();
    }

    protected static ?string $navigationGroup = 'Manajemen Pengguna';
    protected static ?int $navigationSort = 1; // Paling atas di grup "Manajemen Pengguna"

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', UserRole::PENGAJAR);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->helperText('Kosongkan jika tidak ingin mengubah password.'),
                Forms\Components\FileUpload::make('avatar_url')
                    ->label('Foto Profil')
                    ->image()
                    ->disk('public')->directory('avatars/pengajar'),
                    Forms\Components\Hidden::make(name: 'role')->default(UserRole::PENGAJAR->value),
                    Forms\Components\Section::make('Kelas yang Diajar')
                    ->schema([
                        Forms\Components\Select::make('mengajarDiKelas') // Nama relasi di model User
                            ->label('Pilih Kelas yang Diajar')
                            ->multiple()
                            ->relationship(
                                name: 'mengajarDiKelas', // Nama relasi
                                titleAttribute: 'nama_kelas' // Kolom yang ditampilkan dari model Kelas
                                // Anda bisa menambahkan modifyQueryUsing jika perlu filter kelas yang tampil
                                // modifyQueryUsing: fn (Builder $query) => $query->where('aktif', true) 
                            )
                            ->preload() // Memuat opsi di awal
                            ->searchable()
                            ->helperText('Pilih satu atau lebih kelas yang akan diajar oleh pengajar ini.'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')->label('Foto')->circular()->defaultImageUrl(fn(User $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('mengajarDiKelas.nama_kelas') // Menampilkan nama kelas dari relasi
                ->label('Mengajar di Kelas')
                ->badge()
                ->listWithLineBreaks() // Jika ingin setiap kelas di baris baru
                ->limitList(2) // Batasi jumlah kelas yang ditampilkan langsung
                ->expandableLimitedList(), // Sisanya bisa dilihat dengan klik "show more"
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
    
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = UserRole::PENGAJAR->value;
        $data['email_verified_at'] = now();
        return $data;
    }

    public static function getRelations(): array
    {
        return [
            // Relation manager untuk kelas yang diajar bisa ditambahkan di sini nanti
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajars::route('/'),
            'create' => Pages\CreatePengajar::route('/create'),
            'edit' => Pages\EditPengajar::route('/{record}/edit'),
        ];
    }

    // Otorisasi: Admin dan Akademik bisa CRUD Staf Pengajar
    public static function canViewAny(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canCreate(): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canView(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canEdit(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
    public static function canDelete(Model $record): bool { $user = Auth::user(); return $user && ($user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK); }
}
