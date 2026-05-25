<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->string('section_code')->nullable()->unique();
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->foreignId('college_id')->nullable()->constrained('colleges')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['college_id']);
            $table->dropColumn(['section_code', 'department_id', 'college_id']);
        });
    }
};
