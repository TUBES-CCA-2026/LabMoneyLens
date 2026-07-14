<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find or create 'Konsumsi'
        $konsumsiId = DB::table('jenis_pengeluaran')->where('nama_jenis', 'Konsumsi')->value('id_jenis_pengeluaran');
        if (!$konsumsiId) {
            $konsumsiId = DB::table('jenis_pengeluaran')->insertGetId([
                'nama_jenis' => 'Konsumsi',
                'isAktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Get IDs of 'Makanan' and 'Minuman'
        $oldCategoryIds = DB::table('jenis_pengeluaran')
            ->whereIn('nama_jenis', ['Makanan', 'Minuman'])
            ->pluck('id_jenis_pengeluaran')
            ->toArray();

        if (!empty($oldCategoryIds)) {
            // Update pengeluaran records
            DB::table('pengeluaran')
                ->whereIn('id_jenis_pengeluaran', $oldCategoryIds)
                ->update(['id_jenis_pengeluaran' => $konsumsiId]);

            // Soft delete old categories if you want, but since jenis_pengeluaran doesn't use softDeletes by default,
            // we will just set them to isAktif = false or delete them. We will delete them.
            DB::table('jenis_pengeluaran')->whereIn('id_jenis_pengeluaran', $oldCategoryIds)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is difficult as we don't know which 'Konsumsi' was 'Makanan' and which was 'Minuman'.
        // We will just re-insert Makanan and Minuman if they don't exist.
        $names = ['Makanan', 'Minuman'];
        foreach ($names as $name) {
            if (!DB::table('jenis_pengeluaran')->where('nama_jenis', $name)->exists()) {
                DB::table('jenis_pengeluaran')->insert([
                    'nama_jenis' => $name,
                    'isAktif' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
};
