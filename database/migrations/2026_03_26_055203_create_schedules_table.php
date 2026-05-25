<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('room');
            $table->string('subject_code');
            $table->enum('day', ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']);
            $table->time('time_in');
            $table->time('time_out');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};