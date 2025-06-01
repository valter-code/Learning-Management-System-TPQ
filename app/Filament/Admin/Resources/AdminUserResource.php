<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdminUserResource\Pages;
use App\Models\User;
use App\Enums\UserRole; // Pastikan Enum UserRole di-import
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Untuk otorisasi
use Illuminate\Database\Eloquent\Model; // Untuk type hint di can...

class AdminUserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Manajemen Admin';
    protected static ?string $modelLabel = 'Administrator';
    protected static ?string $pluralModelLabel = 'Administrator';
    protected static ?string $slug = 'administrator';

    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    protected static ?string $navigationBadgeTooltip = 'Jumlah Admin';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('role', UserRole::ADMIN)->count();
    }
    protected static ?int $navigationSort = 0; // Paling atas di grup "Manajemen Pengguna"

    // Filter query agar hanya menampilkan user dengan role 'ADMIN'
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', UserRole::ADMIN);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap Admin')
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
                    // Wajib hanya saat create, atau jika field diisi saat edit
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state)) // Hanya kirim ke DB jika diisi
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null) // Hash jika diisi
                    ->helperText('Kosongkan jika tidak ingin mengubah password saat edit. Minimal 8 karakter jika diisi.'),
                Forms\Components\FileUpload::make('avatar_url')
                    ->label('Foto Profil (Opsional)')
                    ->image()
                    ->disk('public')->directory('avatars/admins'),
                // Role diset otomatis, tidak perlu input manual
                Forms\Components\Hidden::make(name: 'role')->default(UserRole::ADMIN->value),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')->label('Foto')->circular()->defaultImageUrl(fn(User $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                Tables\Columns\TextColumn::make('name')->label('Nama Admin')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Dibuat')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tidak banyak filter yang relevan untuk daftar admin, mungkin berdasarkan tanggal dibuat
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    // Tambahkan proteksi agar admin tidak bisa menghapus dirinya sendiri
                    ->visible(fn (User $record): bool => $record->id !== Auth::id()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        // Mungkin ada logika tambahan untuk bulk delete (misal tidak bisa delete diri sendiri)
                        ->action(function (Collection $records) {
                            $records->filter(fn ($record) => $record->id !== Auth::id())->each->delete();
                        }),
                ]),
            ]);
    }
    
    // Metode ini penting untuk memastikan 'role' diset ke 'ADMIN' saat membuat record baru
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = UserRole::ADMIN->value;
        $data['email_verified_at'] = now(); // Admin baru langsung terverifikasi emailnya
        return $data;
    }
    
    // Untuk memastikan 'role' tidak bisa diubah saat edit oleh form ini
    protected static function mutateFormDataBeforeSave(array $data, Model $record): array
    {
        // Jika password tidak diisi saat edit, jangan update passwordnya
        if (empty($data['password'])) {
            unset($data['password']);
        }
        // Pastikan role tidak diubah dari form ini
        unset($data['role']); 
        return $data;
    }

    public static function getRelations(): array
    {
        return [
            // Tidak ada relasi spesifik yang perlu ditampilkan di sini untuk admin
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'edit' => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }

    // Otorisasi: Hanya Admin yang bisa CRUD Admin lain
    // Anda mungkin ingin lebih spesifik, misal hanya SUPER_ADMIN yang bisa kelola ADMIN
    public static function canViewAny(): bool { return Auth::user()?->role === UserRole::ADMIN; }
    public static function canCreate(): bool { return Auth::user()?->role === UserRole::ADMIN; }
    public static function canView(Model $record): bool { return Auth::user()?->role === UserRole::ADMIN; }
    public static function canEdit(Model $record): bool { return Auth::user()?->role === UserRole::ADMIN; }
    public static function canDelete(Model $record): bool 
    { 
        // Admin tidak bisa menghapus dirinya sendiri
        return Auth::user()?->role === UserRole::ADMIN && $record->id !== Auth::id(); 
    }
    public static function canDeleteAny(): bool { return Auth::user()?->role === UserRole::ADMIN; } // Untuk bulk delete
}
