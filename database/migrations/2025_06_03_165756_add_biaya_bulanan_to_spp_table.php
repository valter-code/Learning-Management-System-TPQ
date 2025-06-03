<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Spp;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spp', function (Blueprint $table) {
            
            $table->decimal('biaya_bulanan', 15, 2)->default(0.00)->after('tahun')->comment('Biaya SPP yang seharusnya untuk bulan tersebut');
        });

        Log::info('Attempting to backfill biaya_bulanan from jumlah_bayar for existing SPP records.');
        try {
            $sppsToUpdate = Spp::whereNull('biaya_bulanan')->orWhere('biaya_bulanan', 0.00)->get();
            if ($sppsToUpdate->isNotEmpty()) {
                foreach ($sppsToUpdate as $spp) {
                    if (is_numeric($spp->jumlah_bayar)) {
                        $spp->biaya_bulanan = (float)$spp->jumlah_bayar;
                        $spp->saveQuietly(); // saveQuietly agar tidak trigger event jika ada
                    }
                }
                Log::info('Successfully backfilled biaya_bulanan for ' . $sppsToUpdate->count() . ' records.');
            } else {
                Log::info('No SPP records found needing biaya_bulanan backfill from jumlah_bayar.');
            }

            // Jika masih ada yang null/0, coba dari setting default terakhir jika ada
            $sppsStillNeedingUpdate = Spp::whereNull('biaya_bulanan')->orWhere('biaya_bulanan', 0.00)->get();
            if ($sppsStillNeedingUpdate->isNotEmpty()) {
                $defaultSppAmount = Setting::where('key', 'sistem.jumlah_spp_default')->first()?->value;
                if ($defaultSppAmount && is_numeric($defaultSppAmount)) {
                    Log::info('Attempting to backfill remaining biaya_bulanan from settings default: ' . $defaultSppAmount);
                    foreach ($sppsStillNeedingUpdate as $spp) {
                        $spp->biaya_bulanan = (float)$defaultSppAmount;
                        $spp->saveQuietly();
                    }
                    Log::info('Successfully backfilled remaining biaya_bulanan for ' . $sppsStillNeedingUpdate->count() . ' records using settings default.');
                } else {
                    Log::info('No default SPP amount found in settings or it is not numeric. Skipping further backfill for remaining records.');
                }
            }

        } catch (\Exception $e) {
            Log::error('Error during biaya_bulanan backfill: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spp', function (Blueprint $table) {
            $table->dropColumn('biaya_bulanan');
        });
    }
};
