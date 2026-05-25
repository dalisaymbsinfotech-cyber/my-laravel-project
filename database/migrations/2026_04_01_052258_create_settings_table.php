<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('EARIST');
            $table->string('system_name')->default('EARIST LMS');
            $table->string('logo_path')->nullable();
            $table->string('admin_username')->default('admin');
            $table->string('admin_password');
            $table->json('section_options')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};