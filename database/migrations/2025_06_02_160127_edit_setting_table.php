<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Pastikan kolom yang diperlukan ada
            if (!Schema::hasColumn('settings', 'description')) {
                $table->text('description')->nullable()->after('value');
            }
            
            // Tambahkan index untuk performa
            if (!Schema::hasIndex('settings', 'settings_key_index')) {
                $table->index('key');
            }
        });

        // Insert default settings jika belum ada
        $defaultSettings = [
            [
                'key' => 'contact_address',
                'value' => '',
                'description' => 'Alamat lengkap TPQ untuk ditampilkan di website'
            ],
            [
                'key' => 'contact_phone', 
                'value' => '',
                'description' => 'Nomor telepon TPQ yang bisa dihubungi'
            ],
            [
                'key' => 'contact_email',
                'value' => '',
                'description' => 'Email resmi TPQ'
            ],
            [
                'key' => 'contact_maps_iframe',
                'value' => '',
                'description' => 'Kode embed Google Maps untuk lokasi TPQ'
            ],
        ];

        foreach ($defaultSettings as $setting) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description']
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['key']);
            $table->dropColumn('description');
        });
    }
};