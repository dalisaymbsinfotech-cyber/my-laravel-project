<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classroom_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->string('academic_year');
            $table->enum('semester', ['1st Semester', '2nd Semester']);
            $table->enum('day', ['Monday','Tuesday','Wednesday','Thursday','Friday']);
            $table->string('room_no');
            $table->date('date_of_use');
            $table->time('time_in');
            $table->time('time_out');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_schedules');
    }
};