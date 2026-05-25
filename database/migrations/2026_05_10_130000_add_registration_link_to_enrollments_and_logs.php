<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('face_registration_log_id')->nullable()->after('section');
        });

        Schema::table('face_registration_logs', function (Blueprint $table) {
            $table->foreignId('enrollment_id')->nullable()->after('face_id');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('face_registration_log_id');
        });

        Schema::table('face_registration_logs', function (Blueprint $table) {
            $table->dropColumn('enrollment_id');
        });
    }
};
