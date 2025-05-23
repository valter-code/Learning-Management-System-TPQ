<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PengumumanStatus; // Import Enum

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('ID User yang membuat/mengupdate terakhir')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->string('slug')->unique()->nullable(); 
            $table->longText('konten');
            $table->string('foto')->nullable(); 
            $table->string('status')->default(PengumumanStatus::DRAFT->value); 
            $table->timestamp('published_at')->nullable(); 
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};