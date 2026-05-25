<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'company_name')) {
                $table->string('company_name')->nullable()->after('id');
            }
        });

        foreach (DB::table('settings')->get() as $row) {
            DB::table('settings')->where('id', $row->id)->update([
                'company_name' => $row->system_name ?: 'EARIST',
                'system_name' => 'School Admin System',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('company_name');
        });
    }
};
