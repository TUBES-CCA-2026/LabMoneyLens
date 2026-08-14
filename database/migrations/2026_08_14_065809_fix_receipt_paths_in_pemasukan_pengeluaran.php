<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ambil semua file di folder receipts
        $receiptsPath = storage_path('app/public/receipts');
        if (File::exists($receiptsPath)) {
            $files = File::files($receiptsPath);
            $filenames = collect($files)->map(fn($file) => $file->getFilename())->toArray();
            
            // Fix pemasukan table
            if (count($filenames) > 0) {
                DB::table('pemasukan')
                    ->whereNotNull('foto_bukti')
                    ->where('foto_bukti', '!=', '')
                    ->get()
                    ->each(function($record) use ($filenames) {
                        $currentPath = $record->foto_bukti;
                        
                        // Jika path sudah benar (dimulai dengan receipts/), skip
                        if (str_starts_with($currentPath, 'receipts/')) {
                            return;
                        }
                        
                        // Ekstrak nama file
                        $filename = basename($currentPath);
                        
                        // Cek apakah file ada di folder receipts
                        if (in_array($filename, $filenames)) {
                            DB::table('pemasukan')
                                ->where('id_pemasukan', $record->id_pemasukan)
                                ->update(['foto_bukti' => 'receipts/' . $filename]);
                        }
                    });
            }
            
            // Fix pengeluaran table
            if (count($filenames) > 0) {
                DB::table('pengeluaran')
                    ->whereNotNull('foto_struk')
                    ->where('foto_struk', '!=', '')
                    ->get()
                    ->each(function($record) use ($filenames) {
                        $currentPath = $record->foto_struk;
                        
                        // Jika path sudah benar (dimulai dengan receipts/), skip
                        if (str_starts_with($currentPath, 'receipts/')) {
                            return;
                        }
                        
                        // Ekstrak nama file
                        $filename = basename($currentPath);
                        
                        // Cek apakah file ada di folder receipts
                        if (in_array($filename, $filenames)) {
                            DB::table('pengeluaran')
                                ->where('id_pengeluaran', $record->id_pengeluaran)
                                ->update(['foto_struk' => 'receipts/' . $filename]);
                        }
                    });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu di-revert
    }
};
