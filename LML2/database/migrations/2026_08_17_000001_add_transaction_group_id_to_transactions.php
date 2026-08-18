<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemasukan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemasukan', 'transaction_group_id')) {
                $table->uuid('transaction_group_id')->nullable()->after('id_user');
                $table->index('transaction_group_id');
            }
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            if (!Schema::hasColumn('pengeluaran', 'transaction_group_id')) {
                $table->uuid('transaction_group_id')->nullable()->after('id_user');
                $table->index('transaction_group_id');
            }
        });

        // Backfill legacy records using the same grouping rule that the old application used.
        // This preserves existing transaction groups while replacing the timestamp as the
        // identifier used by application code going forward.
        foreach (['pemasukan', 'pengeluaran'] as $table) {
            $createdAtValues = DB::table($table)
                ->whereNull('transaction_group_id')
                ->select('created_at')
                ->distinct()
                ->pluck('created_at');

            foreach ($createdAtValues as $createdAt) {
                $groupId = (string) Str::uuid();

                DB::table($table)
                    ->whereNull('transaction_group_id')
                    ->where('created_at', $createdAt)
                    ->update(['transaction_group_id' => $groupId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('pemasukan', function (Blueprint $table) {
            if (Schema::hasColumn('pemasukan', 'transaction_group_id')) {
                $table->dropIndex(['transaction_group_id']);
                $table->dropColumn('transaction_group_id');
            }
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
            if (Schema::hasColumn('pengeluaran', 'transaction_group_id')) {
                $table->dropIndex(['transaction_group_id']);
                $table->dropColumn('transaction_group_id');
            }
        });
    }
};
