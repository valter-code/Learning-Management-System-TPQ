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



    protected static ?string $modelLabel = 'Pengumuman'; 

    protected static ?string $pluralModelLabel = 'Pengumuman'; 

    protected static ?string $navigationLabel = 'Pengumuman'; 

    protected static ?string $slug = 'pengumuman'; 

    protected static ?string $title = 'pengumuman'; 



    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Konten Website'; 
    protected static ?int $navigationSort = 3; // Paling atas di grupnya


    protected static ?string $recordTitleAttribute = 'judul'; 



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

                            ->live(onBlur: true) 

                            ->columnSpanFull(),



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
