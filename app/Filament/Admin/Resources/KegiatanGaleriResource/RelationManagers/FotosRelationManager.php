<?php

namespace App\Filament\Admin\Resources\KegiatanGaleriResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class FotosRelationManager extends RelationManager
{
    protected static string $relationship = 'fotos';
    protected static ?string $recordTitleAttribute = 'judul_foto';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul_foto')->maxLength(255)->columnSpanFull(),
                Forms\Components\Textarea::make('deskripsi_foto')->columnSpanFull(),
                Forms\Components\FileUpload::make('path_file')
                    ->label('Upload Foto')->image()->required()->disk('public')
                    ->directory(fn ($livewire) => 'galeri/' . $livewire->ownerRecord->slug_kegiatan) // Simpan di subfolder per slug kegiatan
                    ->imageEditor()->columnSpanFull(),
                Forms\Components\TextInput::make('urutan_foto')->numeric()->default(0),
                Forms\Components\Toggle::make('is_unggulan')->label('Foto Unggulan?'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('path_file') // Atau judul_foto
            ->columns([
                Tables\Columns\ImageColumn::make('path_file')->disk('public')->label('Foto')->width(80)->height(60),
                Tables\Columns\TextColumn::make('judul_foto')->searchable()->limit(30)->placeholder('Tanpa Judul'),
                Tables\Columns\TextColumn::make('urutan_foto')->sortable(),
                Tables\Columns\IconColumn::make('is_unggulan')->label('Unggulan')->boolean(),
            ])
            ->filters([ /* Filter jika perlu */ ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->reorderable('urutan_foto'); // Memungkinkan drag-and-drop untuk urutan
    }
}
