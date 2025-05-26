<?php

namespace App\Filament\Santri\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Enums\UserRole;
use Filament\Forms\Form;
use App\Models\Pertemuan;
use Filament\Tables\Table;
use App\Models\PertemuanMateri;
use App\Models\PengumpulanTugas;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Enums\StatusPertemuanEnum;
use App\Models\PertemuanTugasItem;

// Untuk Infolist
use Filament\Infolists\Components;
use App\Models\Kelas; // Untuk filter
use App\Models\User;  // Untuk filter
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\Layout\Grid;
use App\Enums\StatusPengumpulanTugasEnum;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Santri\Resources\PertemuanResource\Pages;
use Coolsam\NestedComments\Filament\Infolists\CommentsEntry;
use Filament\Notifications\Notification; // <-- PASTIKAN BARIS INI ADA DAN BENAR
use Filament\Tables\Columns\Layout\Grid as ColumnGrid; // Menggunakan alias untuk Grid
use Filament\Forms\Components\FileUpload as FormFileUpload; // Untuk form di dalam Aksi
use Filament\Forms\Components\RichEditor as FormRichEditor; // Untuk form di dalam Aksi
use Illuminate\Support\Facades\Auth; // Untuk pre-filter berdasarkan kelas santri (opsional)


class PertemuanResource extends Resource
{
    protected static ?string $model = Pertemuan::class;

    // Sembunyikan dari navigasi utama jika hanya diakses dari widget
    protected static bool $shouldRegisterNavigation = false;    // Atau beri nama navigasi jika ingin tetap ada
    // protected static ?string $navigationLabel = 'Jadwal Pertemuan';
    // protected static ?string $navigationIcon = 'heroicon-o-calendar';
    // protected static ?string $navigationGroup = 'Akademik Saya';

    // Santri tidak bisa membuat atau mengedit pertemuan
    public static function canCreate(): bool { return false; }
    
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }
    public static function canDeleteAny(): bool { return false; }


    public static function form(Form $form): Form // Form ini tidak akan banyak dipakai oleh santri
    {
        return $form->schema([]); // Kosongkan atau isi dengan field read-only jika perlu
    }

    public static function table(Table $table): Table
    {
        // Metode table tetap sama
        return $table
            ->columns([
                Stack::make([
                    Grid::make(2)
                        ->schema([
                            TextColumn::make('judul_pertemuan')
                                ->weight(FontWeight::ExtraBold)
                                ->size(TextColumn\TextColumnSize::Large)
                                ->tooltip(fn (Pertemuan $record): string => $record->judul_pertemuan ?? ''),
                            TextColumn::make('tanggal_pertemuan')
                                ->date('d M Y')
                                ->color('gray')
                                ->size(TextColumn\TextColumnSize::Small)
                                ->alignment(Alignment::End),
                                // Menampilkan siapa yang membuat pertemuan
                    
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
                        ->sortable() // Tambahkan sortable
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
                    // ->space(3) // Dihapus, karena ini untuk spacing vertikal
                    ->alignment(Alignment::Start) // Perataan item di dalam flex container
                    // Pastikan kelas Tailwind untuk flex dan spacing horizontal ada di sini
                    ->extraAttributes(['class' => 'flex flex-row items-center space-x-2 mt-3 mb-4']), // space-x-2 untuk jarak antar badge

                    TextColumn::make('status_pertemuan')
                        ->label(null)
                        ->badge()
                        ->color(function ($state): string { // $state di sini adalah objek StatusPertemuanEnum
                            if ($state instanceof StatusPertemuanEnum) {
                                return match ($state) {
                                    StatusPertemuanEnum::DIJADWALKAN => 'warning',
                                    StatusPertemuanEnum::BERLANGSUNG => 'info',
                                    StatusPertemuanEnum::SELESAI => 'success',
                                    StatusPertemuanEnum::DIBATALKAN => 'danger',
                                    default => 'gray',
                                };
                            }
                            return 'gray'; // Fallback jika bukan Enum
                        })
                        ->formatStateUsing(function ($state): string { // $state di sini adalah objek StatusPertemuanEnum
                            if ($state instanceof StatusPertemuanEnum) {
                                return $state->getLabel(); // Gunakan getLabel() dari Enum
                            }
                            return (string) $state; // Fallback jika bukan Enum
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
                    SelectFilter::make('user_id') 
                ->label('Pengajar Pertemuan')
                ->relationship('user', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('role', UserRole::PENGAJAR)) // Menggunakan relasi 'user'
                ->searchable()
                ->preload(),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // ... (Section Detail Pertemuan dan Materi Pelajaran tetap sama) ...
                Components\Section::make('Detail Pertemuan')
                    ->columns(2)
                    ->schema([
                        Components\TextEntry::make('judul_pertemuan')->columnSpanFull()->size(Components\TextEntry\TextEntrySize::Large)->weight(FontWeight::Bold),
                        Components\TextEntry::make('kelas.nama_kelas')->label('Kelas')->size(Components\TextEntry\TextEntrySize::Large)->weight(FontWeight::Bold),
                        Components\TextEntry::make('tanggal_pertemuan')->date('d M Y H:i')->size(Components\TextEntry\TextEntrySize::Large)->weight(FontWeight::Bold),
                        Components\TextEntry::make('status_pengumpulan')
                        ->badge()
                        ->formatStateUsing(function ($state): string { // $state di sini adalah objek StatusPengumpulanTugasEnum atau null
                            if ($state instanceof StatusPengumpulanTugasEnum) {
                                return $state->getLabel(); // Panggil metode getLabel() dari Enum
                            }
                            // Fallback jika karena suatu hal $state bukan objek Enum (misalnya null)
                            if (is_string($state)) { 
                                return ucfirst(str_replace('_', ' ', $state));
                            }
                            return 'N/A'; // Default jika null atau tipe tidak dikenal
                        })
                        ->color(function ($state): string { // $state di sini juga objek StatusPengumpulanTugasEnum atau null
                            if ($state instanceof StatusPengumpulanTugasEnum) {
                                return $state->getColor(); // Panggil metode getColor() dari Enum
                            }
                            return 'gray';
                        }),
                        
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
                            ->markdown()->placeholder('Tidak ada deskripsi.')->columnSpanFull()->weight(FontWeight::SemiBold),
                    ]),

                Components\Section::make('Materi Pelajaran')
                    ->collapsible()
                    ->schema([
                        Components\RepeatableEntry::make('itemsMateri')
                            ->label(null)
                            ->schema([
                                Components\TextEntry::make('judul_materi')->label('Judul')->weight(FontWeight::SemiBold),
                                Components\TextEntry::make('tipe_materi')->label('Tipe')->badge()
                                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? '')),
                                Components\TextEntry::make('deskripsi_materi')->label('Deskripsi')->markdown()->placeholder('N/A')->columnSpanFull()->weight(FontWeight::SemiBold),
                                Components\TextEntry::make('url_link_materi')->label('Link Materi')->url(fn (?string $state): ?string => $state)->openUrlInNewTab()->visible(fn (PertemuanMateri $record) => $record->tipe_materi === 'link' && filled($record->url_link_materi))->placeholder(null)->columnSpanFull()->size(Components\TextEntry\TextEntrySize::Medium),
                                Components\TextEntry::make('path_file_materi')->label('File Materi')->formatStateUsing(fn (?string $state): string => $state ? basename($state) : 'Tidak ada file')->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null, shouldOpenInNewTab: true)->visible(fn (PertemuanMateri $record) => $record->tipe_materi === 'file' && filled($record->path_file_materi))->placeholder(null)->icon('heroicon-o-document-arrow-down')->columnSpanFull(),
                                Components\TextEntry::make('konten_text_materi')->label('Isi Materi')->markdown()->visible(fn (PertemuanMateri $record) => $record->tipe_materi === 'text' && filled($record->konten_text_materi))->placeholder(null)->columnSpanFull(),
                            ])->grid(1)->placeholder('Tidak ada materi untuk pertemuan ini.'),
                    ])->columnSpanFull(),


                    Components\Section::make('Tugas Terkait')
                    ->collapsible()
                    ->schema([
                        Components\RepeatableEntry::make('itemsTugas')
                            ->label(null)
                            ->schema([
                                Components\TextEntry::make('judul_tugas')->label('Judul Tugas')->weight(FontWeight::SemiBold),
                                Components\TextEntry::make('deadline_tugas')->dateTime('d M Y H:i')->label('Deadline')->weight(FontWeight::SemiBold),
                                Components\TextEntry::make('deskripsi_tugas')
                                    ->label('Deskripsi')
                                    ->markdown()->placeholder('N/A')->columnSpanFull()->weight(FontWeight::SemiBold),
                                Components\TextEntry::make('file_lampiran_tugas')
                                    ->label('Lampiran Soal')
                                    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : 'Tidak ada lampiran')
                                    
                                    ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null, shouldOpenInNewTab: true)
                                    ->visible(fn (?string $state): bool => filled($state))
                                    ->icon('heroicon-o-document-arrow-down')
                                    ->columnSpanFull(),
    
                                // Menampilkan detail pengumpulan santri
                                Components\Fieldset::make('Pengumpulan Anda')
                                    ->label(function (PertemuanTugasItem $record): string {
                                        $pengumpulan = $record->pengumpulan_santri;
                                        if (!$pengumpulan) return 'Status Pengumpulan Anda';
                                        return 'Detail Pengumpulan Anda (Tgl: ' . ($pengumpulan->tanggal_pengumpulan ? $pengumpulan->tanggal_pengumpulan->format('d M Y, H:i') : 'N/A') . ')';
                                    })
                                    ->schema([
                                        Components\TextEntry::make('status_info')
                                            ->label('Status')
                                            ->getStateUsing(function (PertemuanTugasItem $record): string {
                                                $pengumpulan = $record->pengumpulan_santri;
                                                if (!$pengumpulan) return 'Belum dikerjakan';
                                                $statusText = $pengumpulan->status_pengumpulan->getLabel();
                                                if ($pengumpulan->status_pengumpulan === StatusPengumpulanTugasEnum::TERLAMBAT) $statusText .= " (Terlambat)";
                                                if ($pengumpulan->status_pengumpulan === StatusPengumpulanTugasEnum::DINILAI) $statusText .= " | Nilai: " . ($pengumpulan->nilai ?? 'Belum Dinilai');
                                                return $statusText;
                                            })
                                            ->badge()
                                            ->color(function (PertemuanTugasItem $record): string {
                                                $pengumpulan = $record->pengumpulan_santri;
                                                if (!$pengumpulan) return 'danger';
                                                if ($pengumpulan->status_pengumpulan === 'dinilai') return 'success';
                                                if ($pengumpulan->status_pengumpulan === 'dikumpulkan') return 'primary';
                                                if ($pengumpulan->status_pengumpulan === 'terlambat') return 'warning';
                                                return 'gray';
                                            }),
                                        Components\TextEntry::make('jawaban_teks_santri')
                                            ->label('Jawaban Teks Anda')
                                            ->getStateUsing(fn (PertemuanTugasItem $record) => $record->pengumpulan_santri?->teks_jawaban)
                                            ->markdown()
                                            ->placeholder('Tidak ada jawaban teks.')
                                            ->visible(fn (PertemuanTugasItem $record) => filled($record->pengumpulan_santri?->teks_jawaban)),
                                        Components\TextEntry::make('file_jawaban_santri')
                                            ->label('File Jawaban Anda')
                                            ->getStateUsing(fn (PertemuanTugasItem $record) => $record->pengumpulan_santri?->file_jawaban)
                                            ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '')
                                            ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null, shouldOpenInNewTab: true)
                                            ->icon('heroicon-o-arrow-down-tray') // Ikon berbeda untuk file jawaban
                                            ->visible(fn (PertemuanTugasItem $record) => filled($record->pengumpulan_santri?->file_jawaban)),
                                        Components\TextEntry::make('komentar_pengajar_view')
                                            ->label('Komentar Pengajar')
                                            ->getStateUsing(fn (PertemuanTugasItem $record) => $record->pengumpulan_santri?->komentar_pengajar)
                                            ->markdown()
                                            ->placeholder('Belum ada komentar.')
                                            ->visible(fn (PertemuanTugasItem $record) => $record->pengumpulan_santri && $record->pengumpulan_santri->status_pengumpulan === 'dinilai' && filled($record->pengumpulan_santri?->komentar_pengajar)),
                                    ])->columnSpanFull()->columns(1), // Fieldset untuk pengumpulan santri
    
                                // Aksi untuk setiap item tugas
                                Components\Actions::make([
                                    Components\Actions\Action::make('kerjakan_atau_edit_tugas')
                                        // ... (Definisi aksi kerjakan/edit tugas Anda yang sudah ada, pastikan label dan visibilitynya dinamis)
                                        ->label(function(PertemuanTugasItem $record): string {
                                            $pengumpulan = $record->pengumpulan_santri;
                                            if (!$pengumpulan) return 'Kerjakan Tugas';
                                            if ($pengumpulan->status_pengumpulan === 'dinilai') return 'Lihat Jawaban (Sudah Dinilai)';
                                            return 'Edit Jawaban';
                                        })
                                        ->form([
                                            FormRichEditor::make('teks_jawaban')
                                                ->label('Jawaban Teks')
                                                ->placeholder('Tulis jawaban Anda di sini...')
                                                ->toolbarButtons([
                                                    'bold',
                                                    'italic',
                                                    'underline',
                                                    'bulletList',
                                                    'orderedList',
                                                ]),
                                            FormFileUpload::make('file_jawaban')
                                                ->label('File Jawaban')
                                                ->directory('tugas-jawaban')
                                                ->preserveFilenames()
                                                ->maxSize(10240)
                                                ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                                ->multiple(false),
                                        ])
                                        ->mountUsing(function (Forms\ComponentContainer $form, PertemuanTugasItem $record) {
                                            $pengumpulan = $record->pengumpulan_santri;
                                            if ($pengumpulan) {
                                                $form->fill([
                                                    'teks_jawaban' => $pengumpulan->teks_jawaban,
                                                ]);
                                            }
                                        })
                                        ->action(function (array $data, PertemuanTugasItem $record) {
                                            // ... (logika action Anda untuk menyimpan/update PengumpulanTugas) ...
                                            $pengumpulan = $record->pengumpulan_santri; // Menggunakan accessor
                                            if ($pengumpulan && $pengumpulan->status_pengumpulan === 'dinilai') {
                                                Notification::make()->title('Info')->body('Tugas ini sudah dinilai dan tidak bisa diubah.')->warning()->send();
                                                return;
                                            }
                                            if (empty($data['teks_jawaban']) && empty($data['file_jawaban'])) {
                                                if (!$pengumpulan || (empty($pengumpulan->teks_jawaban) && empty($pengumpulan->file_jawaban))) {
                                                    Notification::make()->title('Input Diperlukan')->body('Harap isi jawaban teks atau unggah file.')->danger()->send();
                                                    return;
                                                }
                                            }
                                            $isTerlambat = $record->deadline_tugas && now()->gt($record->deadline_tugas);
                                            $fileJawabanPath = $pengumpulan->file_jawaban ?? null;
                                            if (!empty($data['file_jawaban'])) {
                                                $fileJawabanPath = $data['file_jawaban'];
                                                if (is_array($data['file_jawaban'])) {
                                                    $fileJawabanPath = $data['file_jawaban'][0];
                                                }
                                            }
                                            PengumpulanTugas::updateOrCreate(
                                                ['pertemuan_tugas_item_id' => $record->id, 'santri_id' => auth()->id()],
                                                [
                                                    'teks_jawaban' => $data['teks_jawaban'] ?? $pengumpulan->teks_jawaban ?? null,
                                                    'file_jawaban' => $fileJawabanPath,
                                                    'tanggal_pengumpulan' => now(),
                                                    'status_pengumpulan' => $isTerlambat && (!$pengumpulan || $pengumpulan->status_pengumpulan == 'belum_dikumpulkan') ? 'terlambat' : ($pengumpulan && $pengumpulan->status_pengumpulan == 'terlambat' ? 'terlambat' : 'dikumpulkan'),
                                                ]
                                            );
                                            Notification::make()->title('Sukses')->body('Jawaban tugas berhasil disimpan.')->success()->send();
                                        })
                                        ->visible(function(PertemuanTugasItem $record): bool { // Tombol hanya visible jika belum dinilai
                                            $pengumpulan = $record->pengumpulan_santri;
                                            return !$pengumpulan || $pengumpulan->status_pengumpulan !== 'dinilai';
                                        })
                                        ->button()->color('primary')->size('xs'), // Sesuaikan warna
                                ])->alignment(Alignment::Start)->label(null),
                            ])
                            ->grid(1)
                            ->placeholder('Tidak ada tugas untuk pertemuan ini.'),
                    ])->columnSpanFull(),

                Components\Section::make('Diskusi & Komentar')
                    ->collapsible()
                    ->schema([
                        CommentsEntry::make('comments')->columnSpanFull(),
                    ])->columnSpanFull(),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPertemuans::route('/'),
            // Santri tidak membuat atau mengedit pertemuan
            // 'create' => Pages\CreatePertemuan::route('/create'),
            // 'edit' => Pages\EditPertemuan::route('/{record}/edit'),
            'view' => Pages\ViewPertemuan::route('/{record}'),
        ];
    }
}