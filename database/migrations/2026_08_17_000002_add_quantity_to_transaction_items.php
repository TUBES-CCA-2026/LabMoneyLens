<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['pemasukan', 'pengeluaran'] as $table) {
            if (!Schema::hasColumn($table, 'quantity')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->unsignedInteger('quantity')->default(1)->after('nominal');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['pemasukan', 'pengeluaran'] as $table) {
            if (Schema::hasColumn($table, 'quantity')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('quantity');
                });
            }
        }
    }
};
