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
            //
        });

        $defaultSettings = [
            [
                'key' => 'visi',
                'value' => 'Menjadi lembaga pendidikan Al-Qur\'an yang unggul, menyenangkan, dan berakhlak mulia',
                'description' => 'Visi lembaga'
            ],
            [
                'key' => 'misi',
                'value' => "1. Menyelenggarakan pembelajaran Al-Qur'an yang efektif, inovatif, dan menyenangkan.\n2. Membina santri agar memiliki pemahaman Al-Qur'an yang baik dan mampu mengamalkannya.\n3. Mengembangkan potensi santri dalam bidang akademik, non-akademik, dan keagamaan.\n4. Menanamkan nilai-nilai Islam dan akhlakul karimah dalam setiap aspek pendidikan.\n5. Membangun kerjasama yang erat dengan orang tua dan masyarakat.",
                'description' => 'Misi lembaga (pisahkan per baris)'
            ],
            [
                'key' => 'sejarah_singkat',
                'value' => 'TPQ kami berdiri sejak 2010 dengan semangat mendidik generasi cinta Al-Qur\'an...',
                'description' => 'Sejarah singkat lembaga'
            ]
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
            //
        });
    }
};
