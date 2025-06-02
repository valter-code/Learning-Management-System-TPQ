<?php

namespace App\Filament\Pages\Shared;

use Filament\Pages\Page;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use App\Models\Setting;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

abstract class BaseAturKontakDanAlamat extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.shared.atur-kontak-dan-alamat';
    
    // Properti untuk menampung data form
    public ?array $data = [];

    // Konstanta untuk key settings - ini memastikan konsistensi
    protected const SETTING_KEYS = [
        'contact_address' => 'contact_address',
        'contact_phone' => 'contact_phone', 
        'contact_email' => 'contact_email',
        'contact_maps_iframe' => 'contact_maps_iframe',
    ];

    public function mount(): void
    {
        // Ambil data dari database menggunakan key yang konsisten
        $this->data = [
            'contact_address' => Setting::getValue(self::SETTING_KEYS['contact_address']),
            'contact_phone' => Setting::getValue(self::SETTING_KEYS['contact_phone']),
            'contact_email' => Setting::getValue(self::SETTING_KEYS['contact_email']),
            'contact_maps_iframe' => Setting::getValue(self::SETTING_KEYS['contact_maps_iframe']),
        ];
        
        // Isi form dengan data
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('contact_address')
                    ->label('Alamat Lengkap TPQ')
                    ->rows(3)
                    ->helperText('Masukkan alamat lengkap yang akan ditampilkan di website.')
                    ->columnSpanFull(),
                    
                TextInput::make('contact_phone')
                    ->label('Nomor Telepon TPQ')
                    ->tel()
                    ->helperText('Contoh: 021-1234567 atau 081234567890')
                    ->maxLength(20),
                    
                TextInput::make('contact_email')
                    ->label('Alamat Email TPQ')
                    ->email()
                    ->helperText('Email resmi yang bisa dihubungi.')
                    ->maxLength(100),
                    
                Textarea::make('contact_maps_iframe')
                    ->label('Kode Sematan (Embed Code) Google Maps')
                    ->rows(5)
                    ->helperText('Salin kode iframe dari Google Maps. Contoh: <iframe src="..."></iframe>')
                    ->columnSpanFull(),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function submit(): void
    {
        // Validasi form terlebih dahulu
        $this->validate();
        
        $formData = $this->form->getState();

        // Simpan menggunakan key yang konsisten
        foreach (self::SETTING_KEYS as $formKey => $settingKey) {
            Setting::setValue(
                $settingKey, 
                $formData[$formKey] ?? '',
                $this->getSettingDescription($settingKey)
            );
        }

        // Update properti data dengan nilai terbaru
        $this->data = $formData;

        // Clear cache untuk memastikan data terbaru tampil di frontend
        $this->clearSettingsCache();

        Notification::make()
            ->title('Pengaturan Kontak & Alamat Berhasil Disimpan')
            ->body('Data akan segera tampil di website dan panel lainnya.')
            ->success()
            ->send();
    }

    /**
     * Clear cache terkait settings
     */
    protected function clearSettingsCache(): void
    {
        $cacheKeys = [
            'contact_settings',
            'setting_contact_address',
            'setting_contact_phone', 
            'setting_contact_email',
            'setting_contact_maps_iframe'
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get description for setting key
     */
    protected function getSettingDescription(string $key): string
    {
        $descriptions = [
            'contact_address' => 'Alamat lengkap TPQ untuk ditampilkan di website',
            'contact_phone' => 'Nomor telepon TPQ yang bisa dihubungi',
            'contact_email' => 'Email resmi TPQ',
            'contact_maps_iframe' => 'Kode embed Google Maps untuk lokasi TPQ',
        ];

        return $descriptions[$key] ?? '';
    }

    /**
     * Metode untuk judul halaman - bisa di-override di child class
     */
    public function getTitle(): string
    {
        return 'Pengaturan Kontak & Alamat Website';
    }

    /**
     * Get current contact data - bisa digunakan untuk debugging
     */
    public function getCurrentData(): array
    {
        return Setting::getMultiple(array_values(self::SETTING_KEYS));
    }
}