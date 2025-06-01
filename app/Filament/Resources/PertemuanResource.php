<?php


namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kelas;
use App\Enums\UserRole;
use Filament\Forms\Form;
use App\Models\Pertemuan;
use Filament\Tables\Table;
use App\Models\PertemuanMateri;
use App\Models\PengumpulanTugas;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Enums\StatusPertemuanEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\ComponentContainer;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\PertemuanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\Layout\Grid as ColumnGrid;
use App\Enums\StatusPengumpulanTugasEnum; // Import Enum 
use Filament\Forms\Components\RichEditor as FormRichEditor;
use Coolsam\NestedComments\Filament\Infolists\CommentsEntry;
use App\Filament\Resources\PertemuanResource\RelationManagers;
use Filament\Infolists\Components; 
use Filament\Forms\Components\ToggleButtons as FormToggleButtons; 
use Filament\Forms\Components\TextInput as FormTextInput; 
use HusamTariq\FilamentTimePicker\Forms\Components\TimePickerField;


class PertemuanResource extends Resource
{
    protected static ?string $model = Pertemuan::class;

    // protected static ?string $slug = 'pertemuan';

    // protected static ?string $title = 'pertemuan';

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Pertemuan';
    protected static ?string $modelLabel = 'Pertemuan';
    protected static ?string $pluralModelLabel = 'Pertemuan';
    protected static ?string $slug = 'pertemuan';
    protected static ?string $navigationGroup = 'Kelas';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('kelas_id') // <-- FIELD PENTING
                ->label('Kelas')
                ->options(function () {
                    /** @var \App\Models\User $user */
                    $user = Auth::user();
                    // Pastikan user adalah pengajar dan memiliki relasi mengajarDiKelas
                    if ($user && $user->role === UserRole::PENGAJAR && method_exists($user, 'mengajarDiKelas')) {
                        // Ambil kelas yang diajar oleh pengajar saat ini
                        return $user->mengajarDiKelas()->pluck('nama_kelas', 'kelas_id')->all();
                    }
                    // Jika bukan pengajar atau tidak ada relasi (seharusnya tidak terjadi di panel pengajar)
                    // atau jika admin bisa mengakses ini dan ingin melihat semua kelas
                    // return Kelas::all()->pluck('nama_kelas', 'id')->all(); 
                    return []; // Untuk pengajar, jika tidak ada kelas yang diajar, tampilkan kosong
                })
                ->searchable()
                ->required()
                ->helperText('Pilih kelas yang Anda ajar untuk pertemuan ini.'),
            Forms\Components\TextInput::make('judul_pertemuan')
                ->required()
                ->maxLength(255),
            Forms\Components\DatePicker::make('tanggal_pertemuan')
                ->required(),
            TimePickerField::make('waktu_mulai')->label('waktu mulai')->okLabel("Confirm")->cancelLabel("Cancel"),
            Forms\Components\RichEditor::make('deskripsi_pertemuan')->columnSpanFull(),
            // Tambahkan field lain jika ada

            Repeater::make('itemsMateri') // Nama relasi HasMany di model Pertemuan
                ->label('Materi Pelajaran (Input Langsung)')
                ->relationship() // Ini memberi tahu repeater untuk mengelola relasi HasMany
                ->schema([
                    Forms\Components\TextInput::make('judul_materi')->required()->columnSpanFull(),
                    Forms\Components\Select::make('tipe_materi')
                        ->options([
                            'text' => 'Teks',
                            'link' => 'Link URL',
                            'file' => 'Upload File',
                        ])->default('text')->required()->reactive()->columnSpanFull(),
                    Forms\Components\RichEditor::make('konten_text_materi')
                        ->label('Isi Materi (Teks)')
                        ->visible(fn ($get) => $get('tipe_materi') === 'text')->columnSpanFull(),
                    Forms\Components\TextInput::make('url_link_materi')
                        ->label('URL Materi (Link)')
                        ->url()
                        ->visible(fn ($get) => $get('tipe_materi') === 'link')->columnSpanFull(),
                    FileUpload::make('path_file_materi')
                        ->label('Upload File Materi')
                        ->disk('public') // Sesuaikan disk penyimpanan Anda
                        ->directory('pertemuan-materi-files')
                        ->visible(fn ($get) => $get('tipe_materi') === 'file')->columnSpanFull(),
                    Forms\Components\Textarea::make('deskripsi_materi')
                        ->label('Deskripsi Singkat Materi (Opsional)')
                        ->columnSpanFull(),
                ])
                ->addActionLabel('Tambah Materi')
                ->columns(1) // Jumlah kolom di dalam satu item repeater
                ->columnSpanFull()
                ->defaultItems(0), // Jumlah item default saat form create

            Repeater::make('itemsTugas') // Nama relasi HasMany di model Pertemuan
                ->label('Tugas Terkait (Input Langsung)')
                ->relationship()
                ->schema([
                    Forms\Components\TextInput::make('judul_tugas')->required()->columnSpanFull(),
                    Forms\Components\RichEditor::make('deskripsi_tugas')->columnSpanFull(),

                    FileUpload::make('file_lampiran_tugas')
                        ->label('File Lampiran Tugas (Opsional)')
                        ->disk('public')
                        ->directory('pertemuan-tugas-files')
                        ->columnSpanFull(),
                    DateTimePicker::make('deadline_tugas')
                        ->label('Deadline Tugas (Opsional)')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('poin_maksimal_tugas')
                        ->label('Poin Maksimal (Opsional)')
                        ->numeric()
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('catatan_tambahan_tugas')
                        ->label('Catatan Tambahan (Opsional)')
                        ->columnSpanFull(),
                ])
                ->addActionLabel('Tambah Tugas')
                ->columns(1)
                ->columnSpanFull()
                ->defaultItems(0),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Detail Pertemuan')
                    ->columns(2)
                    ->schema([
                        Components\TextEntry::make('judul_pertemuan')->columnSpanFull()->size('xl')->weight('bold'),
                        Components\TextEntry::make('kelas.nama_kelas')->label('Kelas')->size('xl')->weight('bold'),
                        Components\TextEntry::make('tanggal_pertemuan')->date('d M Y H:i')->size('xl')->weight('bold'),
                        Components\TextEntry::make('status_pertemuan')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state instanceof StatusPertemuanEnum ? $state->getLabel() : $state)
                            ->color(fn ($state) => $state instanceof StatusPertemuanEnum ? match ($state) {
                                StatusPertemuanEnum::DIJADWALKAN => 'warning',
                                StatusPertemuanEnum::BERLANGSUNG => 'info',
                                StatusPertemuanEnum::SELESAI => 'success',
                                StatusPertemuanEnum::DIBATALKAN => 'danger',
                                default => 'gray',
                            } : 'gray'),
                        Components\TextEntry::make('deskripsi_pertemuan')
                            ->markdown()
                            ->placeholder('Tidak ada deskripsi.')
                            
                            ->columnSpanFull(),
                    ]),

                Components\Section::make('Materi Pelajaran')
                    ->collapsible()
                    ->schema([
                        Components\RepeatableEntry::make('itemsMateri')
                            ->label(null)
                            ->schema([
                                Components\TextEntry::make('judul_materi')->label('Judul')->weight('medium'),
                                Components\TextEntry::make('tipe_materi')
                                    ->label('Tipe')
                                    ->badge()
                                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? '')),
                                Components\TextEntry::make('deskripsi_materi')->label('Deskripsi')->markdown()->placeholder('N/A')->columnSpanFull(),
                                Components\TextEntry::make('url_link_materi')->label('Link Materi')->url(fn (?string $state): ?string => $state)->openUrlInNewTab()->visible(fn (PertemuanMateri $record) => $record->tipe_materi === 'link' && filled($record->url_link_materi))->placeholder(null)->columnSpanFull(),
                                Components\TextEntry::make('path_file_materi')->label('File Materi')->formatStateUsing(fn (?string $state): string => $state ? basename($state) : 'Tidak ada file')->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null, shouldOpenInNewTab: true)->visible(fn (PertemuanMateri $record) => $record->tipe_materi === 'file' && filled($record->path_file_materi))->placeholder(null)->icon('heroicon-o-document-arrow-down')->columnSpanFull(),
                                Components\TextEntry::make('konten_text_materi')->label('Isi Materi')->markdown()->visible(fn (PertemuanMateri $record) => $record->tipe_materi === 'text' && filled($record->konten_text_materi))->placeholder(null)->columnSpanFull(),
                            ])
                            ->grid(1)
                            ->placeholder('Tidak ada materi untuk pertemuan ini.'),
                    ])->columnSpanFull(),

                Components\Section::make('Tugas Terkait & Pengumpulan Santri')
                    ->collapsible()
                    ->schema([
                        Components\RepeatableEntry::make('itemsTugas') // Ini adalah PertemuanTugasItem
                            ->label(null)
                            ->schema([
                                // Detail Tugas Item (Judul, Deadline, Deskripsi)
                                Components\TextEntry::make('judul_tugas')->label('Judul Tugas')->columnSpanFull(),
                                Components\TextEntry::make('deadline_tugas')->dateTime('d M Y H:i')->label('Deadline')->columnSpanFull(),
                                Components\TextEntry::make('deskripsi_tugas')->label('Deskripsi')->markdown()->placeholder('N/A')->columnSpanFull(),
                                Components\TextEntry::make('file_lampiran_tugas')
                                    ->label('Lampiran Soal')
                                    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : 'Tidak ada lampiran')
                                    ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null, shouldOpenInNewTab: true)
                                    ->visible(fn (?string $state): bool => filled($state))
                                    ->icon('heroicon-o-document-arrow-down')->columnSpanFull(),

                                // Bagian untuk menampilkan daftar santri yang sudah mengumpulkan
                                Components\Section::make('Jawaban Santri') // Judul untuk grup pengumpulan
                                    ->description('Klik nama santri untuk melihat detail dan memberi nilai.')
                                    ->collapsible() // Section ini bisa diciutkan jika ada banyak item tugas
                                    ->collapsed(false) // Default terbuka untuk melihat daftar santri
                                    ->schema([
                                        Components\RepeatableEntry::make('pengumpulanTugas') // Relasi HasMany di PertemuanTugasItem
                                            ->label(null) 
                                            ->schema([
                                                
                                                Components\Section::make() 
                                                    ->heading(fn (PengumpulanTugas $record): string => "Jawaban: " . $record->santri?->name . " (" . ($record->status_pengumpulan instanceof StatusPengumpulanTugasEnum ? $record->status_pengumpulan->getLabel() : ucfirst(str_replace('_', ' ', (string)$record->status_pengumpulan))) .")")
                                                    ->collapsible()
                                                    ->collapsed(true) // Setiap pengumpulan santri defaultnya terlipat
                                                    ->compact() // Membuat section lebih ringkas
                                                    ->schema([
                                                        Components\TextEntry::make('tanggal_pengumpulan')->dateTime('d M Y, H:i')->label('Tgl Kumpul'),
                                                        Components\TextEntry::make('nilai')->label('Nilai')->placeholder('Belum dinilai'),
                                                        Components\TextEntry::make('teks_jawaban')->label('Jawaban Teks')->markdown()->placeholder('Tidak ada jawaban teks.'),
                                                        Components\TextEntry::make('file_jawaban')
                                                            ->label('File Jawaban Santri')
                                                            ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : 'Tidak ada file')
                                                            ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null, true)
                                                            ->icon('heroicon-o-arrow-down-tray')
                                                            ->visible(fn (?string $state) => filled($state)), // Koma ditambahkan di sini
                                                        Components\TextEntry::make('komentar_pengajar')
                                                            ->label('Komentar Anda')
                                                            ->markdown()
                                                            ->lineClamp(3) // Opsional
                                                            ->placeholder('Belum ada komentar.'), // Koma ditambahkan di sini

                                                        // Aksi untuk Pengajar: Koreksi & Beri Nilai
                                                        Components\Actions::make([
                                                            Components\Actions\Action::make('koreksiDanNilai')
                                                                ->label(fn (PengumpulanTugas $record) => $record->status_pengumpulan == StatusPengumpulanTugasEnum::DINILAI ? 'Edit Koreksi' : 'Koreksi & Nilai')
                                                                ->icon('heroicon-o-pencil-square')
                                                                ->form([
                                                                    FormTextInput::make('nilai')->label('Nilai (0-100)')->numeric()->minValue(0)->maxValue(100),
                                                                    FormRichEditor::make('komentar_pengajar')->label('Komentar/Feedback'),
                                                                    FormToggleButtons::make('status_pengumpulan_update')
                                                                        ->label('Status Akhir')
                                                                        ->options(collect(StatusPengumpulanTugasEnum::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])->toArray())
                                                                        ->inline()
                                                                        ->default(StatusPengumpulanTugasEnum::DINILAI->value)
                                                                        ->helperText('Pilih status akhir setelah dinilai.'),
                                                                ])
                                                                ->mountUsing(function (Form $form, PengumpulanTugas $record) {
                                                                    $form->fill([
                                                                        'nilai' => $record->nilai,
                                                                        'komentar_pengajar' => $record->komentar_pengajar,
                                                                        'status_pengumpulan_update' => $record->status_pengumpulan == StatusPengumpulanTugasEnum::DINILAI ? StatusPengumpulanTugasEnum::DINILAI->value : null,
                                                                    ]);
                                                                })
                                                                ->action(function (array $data, PengumpulanTugas $record) {
                                                                    $record->nilai = $data['nilai'];
                                                                    $record->komentar_pengajar = $data['komentar_pengajar'];
                                                                    if (isset($data['status_pengumpulan_update']) && $data['status_pengumpulan_update'] == StatusPengumpulanTugasEnum::DINILAI->value) {
                                                                        $record->status_pengumpulan = StatusPengumpulanTugasEnum::DINILAI;
                                                                    }
                                                                    $record->save();
                                                                    Notification::make()->title('Sukses')->body('Koreksi dan nilai berhasil disimpan.')->success()->send();
                                                                })
                                                                ->modalHeading('Koreksi Jawaban Santri')
                                                                ->modalSubmitActionLabel('Simpan Koreksi')
                                                                ->button()->size('xs'),
                                                        ])->alignment(Alignment::End)->label(null), // Ini elemen terakhir, tidak perlu koma
                                                    ]), // Penutup schema untuk Section per santri
                                            ]) // Penutup schema untuk RepeatableEntry pengumpulanTugas
                                            ->grid(1) // Setiap Section pengumpulan dalam satu baris
                                            ->placeholder('Belum ada santri yang mengumpulkan tugas ini.'),
                                    ])->columnSpanFull(), // Penutup schema untuk Section 'Jawaban Santri'
                            ]) // Penutup schema untuk RepeatableEntry itemsTugas
                            ->grid(1) // Setiap item tugas dalam satu baris di dalam repeater utama
                            ->placeholder('Tidak ada tugas untuk pertemuan ini.'),
                    ])->columnSpanFull(), // Penutup schema untuk Section 'Tugas Terkait & Pengumpulan Santri'

                // BAGIAN UNTUK KOMENTAR
                Components\Section::make('Diskusi & Komentar')
                    ->collapsible()
                    ->schema([
                        CommentsEntry::make('comments')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ])
            ->columns(1); 
    }

    public static function table(Table $table): Table
    {
        
        return $table
            ->columns([
                Stack::make([
                    Grid::make(2)
                        ->schema([
                            TextColumn::make('judul_pertemuan')
                                ->weight(FontWeight::ExtraBold)
                                ->size(TextColumn\TextColumnSize::Large)
                                // ->lineClamp(lineClamp: 2)
                                ->tooltip(fn (Pertemuan $record): string => $record->judul_pertemuan ?? ''),
                            TextColumn::make('tanggal_pertemuan')
                                ->date('d M Y')
                                ->color('gray')
                                ->size(TextColumn\TextColumnSize::Small)
                                ->alignment(Alignment::End),
                    
                    TextColumn::make('user.name')
                    ->label(null)
                    ->prefix('Dibuat oleh: ')
                    ->weight(FontWeight::Medium)
                    ->size(TextColumn\TextColumnSize::Small)
                    ->color('gray')
                    ->icon('heroicon-s-user-circle')
                    ->default('N/A')
                    ->getStateUsing(function (Pertemuan $record): string {
                        return $record->user ? $record->user->name : 'N/A';
                    })
                    ->extraAttributes(['class' => 'mb-1 text-xs']),
                    TextColumn::make('kelas.nama_kelas') // Menampilkan nama kelas dari relasi
                        ->label(null)
                        ->prefix('Kelas: ')
                        ->weight(FontWeight::Medium)
                        ->size(TextColumn\TextColumnSize::Small)
                        ->color('primary') // Warna untuk kelas
                        ->icon('heroicon-s-academic-cap')
                        ->default('N/A')
                        // ->sortable() // Tambahkan sortable
                        // Untuk sortable pada relasi, Anda mungkin perlu query kustom
                        // ->query(function (Builder $query, string $direction): Builder {
                        //     return $query
                        //         ->join('kelas', 'pertemuan.kelas_id', '=', 'kelas.id')
                        //         ->orderBy('kelas.nama_kelas', $direction);
                        // })
                        ->extraAttributes(['class' => 'mb-2 text-xs']),
                        ])
                        ->extraAttributes(['class' => 'mb-3']),
                        // PENYESUAIAN PADA STACK MATERI DAN TUGAS (Kode ini adalah yang Anda pilih)
                    Stack::make([
                        TextColumn::make('materiItems_count_display')
                            ->getStateUsing(fn (Pertemuan $record): int => $record->itemsMateri()->count())
                            ->badge()
                            ->color(function ($state): string {
                                $count = (int) $state;
                                if ($count === 0) return 'gray';
                                return 'primary';
                            })
                            ->formatStateUsing(function ($state): string {
                                $count = (int) $state;
                                if ($count === 0) return '0 Materi';
                                return "{$count} Materi";
                            })
                            ->icon('heroicon-s-book-open')
                            ->label(null),
                        TextColumn::make('tugasItems_count_display')
                            ->getStateUsing(fn (Pertemuan $record): int => $record->itemsTugas()->count())
                            ->badge()
                            ->color(function ($state): string {
                                $count = (int) $state;
                                if ($count === 0) return 'gray';
                                return 'warning';
                            })
                            ->formatStateUsing(function ($state): string {
                                $count = (int) $state;
                                if ($state === 0) return '0 Tugas';
                                return "{$count} Tugas";
                            })
                            ->icon('heroicon-s-clipboard-document-list')
                            ->label(null),
                    ])
                    ->space(3) 
                    ->alignment(Alignment::Start) 
                    ->extraAttributes(['class' => 'flex flex-row items-center space-x-2 mt-3 mb-4']), 

                    TextColumn::make('status_pertemuan')
                        ->label(null)
                        ->badge()
                        ->color(function ($state): string { 
                            if ($state instanceof StatusPertemuanEnum) {
                                return match ($state) {
                                    StatusPertemuanEnum::DIJADWALKAN => 'warning',
                                    StatusPertemuanEnum::BERLANGSUNG => 'info',
                                    StatusPertemuanEnum::SELESAI => 'success',
                                    StatusPertemuanEnum::DIBATALKAN => 'danger',
                                    default => 'gray',
                                };
                            }
                            return 'gray'; 
                        })
                        ->formatStateUsing(function ($state): string { 
                            if ($state instanceof StatusPertemuanEnum) {
                                return $state->getLabel(); 
                            }
                            return (string) $state; 
                        })
                        ->size(TextColumn\TextColumnSize::Small),
                ])
                // ->extraAttributes(['class' => 'bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 p-4 my-3 flex flex-col h-full']),
            ])
            ->contentGrid([
                'default' => 1, 'sm' => 2, 'md' => 2, 'lg' => 3, 'xl' => 3,
            ])
            ->paginated([6, 9, 12, 24, 'all'])
            ->defaultSort('tanggal_pertemuan', 'desc')
            ->filters([
                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->options(function (): array {
                        /** @var \App\Models\User|null $pengajar */
                        $pengajar = Auth::user();
                        if (!$pengajar || !method_exists($pengajar, 'mengajarDiKelas')) {
                            return [];
                        }
                        return $pengajar->mengajarDiKelas()->pluck('nama_kelas', 'kelas.id')->toArray();
                    })
                    ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Pertemuan Baru')
                    ->url(function (Tables\Table $table): ?string {
                        $filterState = $table->getFilter('kelas_id')?->getState();
                        $activeKelasId = $filterState['value'] ?? null;

                        if ($activeKelasId) {
                            return static::getUrl('create', ['active_kelas_id' => $activeKelasId]);
                        }
                        return static::getUrl('create');
                    }),
            ])
            ->actions([
                ViewAction::make()->iconButton()->tooltip('Lihat Detail'),
                EditAction::make()->iconButton()->tooltip('Edit'),
                DeleteAction::make()->iconButton()->tooltip('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        // Log untuk debugging (bisa dihapus setelah berfungsi)
        Log::info('PertemuanResource - mutateFormDataBeforeCreate - Data Awal:', $data);
        Log::info('PertemuanResource - mutateFormDataBeforeCreate - Auth ID:', ['auth_id' => Auth::id()]);

        $data['user_id'] = Auth::id(); // Mengisi user_id dengan ID pengajar yang sedang login

        // Jika Anda juga meneruskan active_kelas_id dari URL (seperti yang sudah ada):
        if (request()->filled('active_kelas_id')) {
            $data['kelas_id'] = request()->query('active_kelas_id');
        }
        
        Log::info('PertemuanResource - mutateFormDataBeforeCreate - Data Akhir (dengan user_id):', $data);
        return $data;
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPertemuans::route('/'),
            'create' => Pages\CreatePertemuan::route('/create'),
            // 'edit' => Pages\EditPertemuan::route('/{record}/edit'), // Komentari atau hapus jika hanya ingin modal
            'view' => Pages\ViewPertemuan::route('/{record}'), // Akan kita buat/pastikan ini ada
        ];
    }
}

