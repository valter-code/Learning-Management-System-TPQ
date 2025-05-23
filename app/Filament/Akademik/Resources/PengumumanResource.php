<?php

namespace App\Filament\Akademik\Resources;

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

                        Select::make('status')

                            ->options(collect(PengumumanStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])->toArray())

                            ->required()

                            ->default(PengumumanStatus::DRAFT->value)

                            ->native(false), 

                        DateTimePicker::make('published_at')

                            ->label('Tanggal Publikasi')

                            ->helperText('Kosongkan jika ingin dipublikasikan segera setelah status diubah ke "Published" atau set tanggal di masa depan untuk penjadwalan.'),

                        

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

                ->label('Ubah Status') 

                ->options(PengumumanStatus::class)

                ->rules(['required', new EnumRule(PengumumanStatus::class)]),

            // TextColumn::make('published_at')

            //     ->dateTime('d M Y H:i')

            //     ->sortable()

            //     ->label('Tgl Publikasi'),

            TextColumn::make('created_at')

                ->dateTime('d M Y')

                ->sortable(),

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



     // Metode untuk mengisi user_id secara otomatis

    //  public static function mutateFormDataBeforeCreate(array $data): array

    //  {

    //      $data['user_id'] = Auth::id();

    //      // Jika menggunakan slug manual dan ingin di-generate dari judul saat create

    //      if (empty($data['slug']) && !empty($data['judul']) && !config('packages.spatie.laravel-sluggable.enabled', false)) { // Cek jika spatie/sluggable tidak dipakai

    //           $data['slug'] = Str::slug($data['judul']);

    //      }

    //      return $data;

    //  }



       // Jika ingin user_id juga diupdate saat edit oleh user yang berbeda (opsional)

    // public static function mutateFormDataBeforeSave(array $data): array

    // {

    //     $data['user_id'] = Auth::id();

    //     return $data;

    // }



   

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
