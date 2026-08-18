<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemasukan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemasukan', 'is_confirmed')) {
                $table->boolean('is_confirmed')->default(false)->after('nominal');
            }
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            if (!Schema::hasColumn('pengeluaran', 'is_confirmed')) {
                $table->boolean('is_confirmed')->default(false)->after('nominal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pemasukan', function (Blueprint $table) {
            if (Schema::hasColumn('pemasukan', 'is_confirmed')) {
                $table->dropColumn('is_confirmed');
            }
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            if (Schema::hasColumn('pengeluaran', 'is_confirmed')) {
                $table->dropColumn('is_confirmed');
            }
        });
    }
};
