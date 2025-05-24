<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Enums\UserRole;
use Filament\Forms\Form;
use App\Models\Pengumuman;
use Filament\Tables\Table;
use App\Enums\PengumumanStatus;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str; // Untuk slug, jika manual
use App\Filament\Admin\Resources\PengumumanResource\Pages;
use App\Filament\Admin\Resources\PengumumanResource\RelationManagers;
use Illuminate\Validation\Rules\Enum as EnumRule; // \<-- Import untuk validasi Enum

class PengumumanResource extends Resource
{
    protected static ?string $model = Pengumuman::class;

    protected static ?string $modelLabel = 'Pengumuman'; // Label tunggal
    protected static ?string $pluralModelLabel = 'Pengumuman'; // Label jamak → tetap gunakan bentuk tunggal
    protected static ?string $navigationLabel = 'Pengumuman'; // Label di sidebar menu
    protected static ?string $slug = 'pengumuman'; // Atau 'pengumumans' jika Anda lebih suka plural
    protected static ?string $title = 'pengumuman'; 
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Konten Website'; // Contoh grup navigasi
    protected static ?string $recordTitleAttribute = 'judul'; // Untuk judul di breadcrumbs dll.

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Konten Pengumuman')
                    ->columns(2)
                    ->schema([
                        TextInput::make('judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true) // Untuk update slug otomatis jika pakai cara manual
                            // ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))) // Manual slug generation
                            ->columnSpanFull(),

                        // Jika Anda menggunakan spatie/laravel-sluggable, field slug tidak perlu ditampilkan di form
                        // karena akan di-generate otomatis. Jika manual, bisa seperti ini:
                        // TextInput::make('slug')
                        //     ->required()
                        //     ->maxLength(255)
                        //     ->unique(Pengumuman::class, 'slug', ignoreRecord: true)
                        //     ->columnSpanFull(),

                        RichEditor::make('konten')
                            ->required()
                            ->columnSpanFull(),

                        FileUpload::make('foto')
                            ->image()
                            ->disk('public') 
                            ->directory('pengumuman-fotos')
                            ->imageEditor() 
                            ->columnSpanFull(),
                    ]),
                Section::make('Status & Publikasi')
                ->schema([
                    Forms\Components\Select::make('status')
                ->options(PengumumanStatus::class)
                ->required()
                ->default(PengumumanStatus::DRAFT->value)
                ->native(false)
                ->reactive() // <-- Membuat field ini reaktif
                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state) {
                    // $state adalah nilai BARU dari field 'status'
                    // $get adalah callable untuk mendapatkan nilai field lain di form
                    if ($state === PengumumanStatus::PUBLISHED->value) {
                        // Cek apakah published_at saat ini kosong di form
                        if (empty($get('published_at'))) {
                            // Set nilai published_at di form menjadi waktu sekarang
                            // DateTimePicker menerima instance Carbon atau string yang bisa diparsing
                            $set('published_at', now()); 
                        }
                    } 
                    // Opsional: jika status diubah BUKAN menjadi published, Anda bisa mengosongkan published_at
                    // else {
                    //     $set('published_at', null);
                    // }
                }),
                Forms\Components\DateTimePicker::make('published_at')
                ->label('Tanggal Publikasi (Opsional)')
                ->helperText('Kosongkan agar dipublikasikan segera saat status "Dipublikasikan", atau pilih tanggal & waktu tertentu.')
                ->native(false)
                ->displayFormat('d/m/Y H:i') 
                ->seconds(false), 
                        
                    ])
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            ImageColumn::make('foto')->disk('public')->label('Foto')->width(100)->height(80),
            TextColumn::make('judul')
                ->searchable()
                ->sortable()
                ->limit(50)
                ->tooltip(fn (Pengumuman $record) => $record->judul),
            TextColumn::make('user.name') 
                ->label('Dibuat Oleh')
                ->searchable()
                ->sortable(),
                TextColumn::make('status_display') 
                ->label('Status') 
                ->state(fn(Pengumuman $record) => $record->status) 
                ->badge()
                ->color(fn ($state) => $state instanceof PengumumanStatus ? $state->getColor() : 'gray')
                ->formatStateUsing(fn ($state) => $state instanceof PengumumanStatus ? $state->getLabel() : $state), 
                SelectColumn::make('status')
                ->label('Status')
                ->options(PengumumanStatus::class)
                ->rules(['required', new EnumRule(PengumumanStatus::class)])
                ->sortable()
                ->afterStateUpdated(function (Pengumuman $record, $state) {
                    // Konversi $state ke Enum jika dari string
                    $newStatus = $state instanceof PengumumanStatus ? $state : PengumumanStatus::tryFrom((string)$state);
    
                    if ($newStatus === PengumumanStatus::PUBLISHED && is_null($record->published_at)) {
                        $record->published_at = now();
                        $record->save();
                    }
                    // Opsional: Jika diubah jadi draft/archived dari tabel, published_at bisa di-null kan
                    // elseif ($newStatus !== PengumumanStatus::PUBLISHED && !is_null($record->published_at)) {
                    //    $record->published_at = null;
                    //    $record->save();
                    // }
                    Notification::make()->title('Status pengumuman diperbarui')->success()->send();
                }),
            TextColumn::make('published_at')
                ->dateTime('d M Y H:i') 
                ->sortable()
                ->label('Tgl Publikasi')
                ->placeholder('Belum dipublikasikan'),
            TextColumn::make('user.role')
            ->label('Role Pembuat')
            ->badge()
            ->formatStateUsing(fn ($state) => $state instanceof UserRole ? $state->getLabel() : $state)
            ->color(fn ($state) => $state instanceof UserRole ? $state->getColor() : 'gray')
            ->sortable(),
            

            TextColumn::make('created_at')
                ->dateTime('d M Y')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->options(collect(PengumumanStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])->toArray())
                ->native(false),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPengumumen::route('/'),
            'create' => Pages\CreatePengumuman::route('/create'),
            'edit' => Pages\EditPengumuman::route('/{record}/edit'),
        ];
    }

    

    // Mengisi user_id dan published_at secara otomatis saat CREATE
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
    $data['user_id'] = Auth::id();

    // Buat slug jika kosong dan tidak pakai Spatie Sluggable
    if (empty($data['slug']) && !empty($data['judul'])) {
        $data['slug'] = Str::slug($data['judul']);
    }

    // Jika status adalah PUBLISHED dan published_at tidak diisi manual, set ke waktu sekarang
    if (isset($data['status']) && $data['status'] === PengumumanStatus::PUBLISHED->value && empty($data['published_at'])) {
        $data['published_at'] = now();
    }
    return $data;

    }

   
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return $user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK;
    }


    
    public static function canCreate(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return $user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK;
    }


    public static function canView(Model $record): bool 
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return $user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK;
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        // Semua admin/akademik bisa mengedit semua pengumuman
        return $user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK;
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return $user->role === UserRole::ADMIN || $user->role === UserRole::AKADEMIK;
    }
}
