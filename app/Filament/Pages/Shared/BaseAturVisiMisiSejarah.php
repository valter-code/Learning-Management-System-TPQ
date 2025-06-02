<?php

namespace App\Filament\Pages\Shared;

use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;

abstract class BaseAturVisiMisiSejarah extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.shared.atur-visi-misi-sejarah'; // View baru
    
    public ?array $data = [];

    // Konstanta untuk key settings
    protected const SETTING_KEYS = [
        'vision' => 'web_vision',
        'mission' => 'web_mission', 
        'brief_history' => 'web_brief_history',
    ];

    public function mount(): void
    {
        $this->data = [
            'vision' => Setting::getValue(self::SETTING_KEYS['vision']),
            'mission' => Setting::getValue(self::SETTING_KEYS['mission']),
            'brief_history' => Setting::getValue(self::SETTING_KEYS['brief_history']),
        ];
        
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('vision')
                    ->label('Visi Website')
                    ->rows(5)
                    ->helperText('Masukkan Visi yang akan ditampilkan di website.')
                    ->columnSpanFull(),
                    
                    RichEditor::make('mission')
                    ->label('Misi Website')
                    ->helperText("Masukkan Misi yang akan ditampilkan di website. Anda dapat menggunakan fitur formatting seperti bullet points atau numbering untuk menyusun poin-poin misi.")
                    ->toolbarButtons([ // Opsional: kustomisasi tombol toolbar
                        'attachFiles',
                        'blockquote',
                        'bold',
                        'bulletList',
                        'codeBlock',
                        'h2',
                        'h3',
                        'italic',
                        'link',
                        'orderedList',
                        'redo',
                        'strike',
                        'underline',
                        'undo',
                    ])
                    ->columnSpanFull(),
                    
                Textarea::make('brief_history')
                    ->label('Sejarah Singkat Website')
                    ->rows(8)
                    ->helperText('Masukkan Sejarah Singkat yang akan ditampilkan di website.')
                    ->columnSpanFull(),
            ])
            ->statePath('data')
            ->columns(1); // Mungkin lebih baik 1 kolom untuk textarea panjang
    }

    public function submit(): void
    {
        $this->validate();
        
        $formData = $this->form->getState();

        foreach (self::SETTING_KEYS as $formKey => $settingKey) {
            Setting::setValue(
                $settingKey, 
                $formData[$formKey] ?? '',
                $this->getSettingDescription($settingKey)
            );
        }

        $this->data = $formData;
        $this->clearSettingsCache();

        Notification::make()
            ->title('Pengaturan Visi, Misi & Sejarah Berhasil Disimpan')
            ->body('Data akan segera tampil di website dan panel lainnya.')
            ->success()
            ->send();
    }

    protected function clearSettingsCache(): void
    {
        // Tambahkan cache keys yang relevan untuk Visi, Misi, Sejarah
        $cacheKeys = [
            'website_content_settings', // Cache umum untuk konten
            'setting_web_vision',
            'setting_web_mission', 
            'setting_web_brief_history',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    protected function getSettingDescription(string $key): string
    {
        $descriptions = [
            'visi' => 'Visi website',
            'misi' => 'Misi website, dipisahkan per baris',
            'sejarah_singkat' => 'Sejarah singkat website',
        ];

        return $descriptions[$key] ?? 'Pengaturan konten website';
    }

    public function getTitle(): string
    {
        return 'Pengaturan Visi, Misi & Sejarah Singkat';
    }

    public function getCurrentData(): array
    {
        return Setting::getMultiple(array_values(self::SETTING_KEYS));
    }
}