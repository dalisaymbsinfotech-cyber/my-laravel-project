<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('face_registration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('id_number')->index();
            $table->string('name');
            $table->foreignId('face_id')->nullable()->constrained('faces')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('face_registration_logs');
    }
};
