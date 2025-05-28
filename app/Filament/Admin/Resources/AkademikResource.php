<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AkademikResource\Pages;
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
use Illuminate\Database\Eloquent\Model; // Untuk can... metode

class AkademikResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Staff Akademik';
    protected static ?string $modelLabel = 'Staff Akademik';
    protected static ?string $pluralModelLabel = 'Staff Akademik';
    protected static ?string $slug = 'staff-akademik';
    protected static ?string $navigationGroup = 'Manajemen Pengguna';
    protected static ?int $navigationSort = 2; // Di bawah Pengajar (atau sesuaikan)

    protected static ?string $navigationBadgeTooltip = 'Jumlah Staff Akademik';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('role', UserRole::PENGAJAR)->count();
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', UserRole::AKADEMIK);
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
                    ->disk('public')->directory('avatars/akademik'),
                // Role diset otomatis, tidak perlu input
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')->label('Foto')->circular()->defaultImageUrl(fn(User $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
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
        $data['role'] = UserRole::AKADEMIK->value;
        $data['email_verified_at'] = now(); // Otomatis verifikasi email saat dibuat Admin
        return $data;
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
            'index' => Pages\ListAkademiks::route('/'),
            'create' => Pages\CreateAkademik::route('/create'),
            'edit' => Pages\EditAkademik::route('/{record}/edit'),
        ];
    }

    // Otorisasi: Hanya Admin yang bisa CRUD Staf Akademik
    public static function canViewAny(): bool { return Auth::user()?->role === UserRole::ADMIN; }
    public static function canCreate(): bool { return Auth::user()?->role === UserRole::ADMIN; }
    public static function canView(Model $record): bool { return Auth::user()?->role === UserRole::ADMIN; }
    public static function canEdit(Model $record): bool { return Auth::user()?->role === UserRole::ADMIN; }
    public static function canDelete(Model $record): bool { return Auth::user()?->role === UserRole::ADMIN; }
}
