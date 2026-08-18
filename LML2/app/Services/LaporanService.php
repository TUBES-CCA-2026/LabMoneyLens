<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LaporanService
{
    public function buildReportQuery(\Illuminate\Http\Request $request): array
    {
        $month = $request->query('month');
        $category = $request->query('category');

        $pemasukanQuery = DB::table('pemasukan')
            ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
            ->select(
                'pemasukan.id_pemasukan as id',
                'jenis_penerimaan.nama_jenis as kategori',
                'pemasukan.nominal as jumlah',
                'pemasukan.tanggal',
                'pemasukan.created_at as created_at',
                'pemasukan.transaction_group_id as transaction_group_id',
                'pemasukan.is_confirmed as is_confirmed',
                'pemasukan.uraian',
                DB::raw("'Pemasukan' as tipe")
            )
            ->whereNull('pemasukan.deleted_at')
            ->where('pemasukan.is_confirmed', 1);

        $pengeluaranQuery = DB::table('pengeluaran')
            ->join('jenis_pengeluaran', 'pengeluaran.id_jenis_pengeluaran', '=', 'jenis_pengeluaran.id_jenis_pengeluaran')
            ->select(
                'pengeluaran.id_pengeluaran as id',
                'jenis_pengeluaran.nama_jenis as kategori',
                'pengeluaran.nominal as jumlah',
                'pengeluaran.tanggal',
                'pengeluaran.created_at as created_at',
                'pengeluaran.transaction_group_id as transaction_group_id',
                'pengeluaran.is_confirmed as is_confirmed',
                'pengeluaran.uraian',
                DB::raw("'Pengeluaran' as tipe")
            )
            ->whereNull('pengeluaran.deleted_at')
            ->where('pengeluaran.is_confirmed', 1);

        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            [$year, $monthNumber] = explode('-', $month);
            $pemasukanQuery->whereYear('pemasukan.tanggal', $year)->whereMonth('pemasukan.tanggal', $monthNumber);
            $pengeluaranQuery->whereYear('pengeluaran.tanggal', $year)->whereMonth('pengeluaran.tanggal', $monthNumber);
        }

        if ($category && $category !== 'semua') {
            $pemasukanQuery->where('jenis_penerimaan.nama_jenis', $category);
            $pengeluaranQuery->where('jenis_pengeluaran.nama_jenis', $category);
        }

        try {
            $pemasukan = $pemasukanQuery->get();
            $pengeluaran = $pengeluaranQuery->get();
        } catch (\Throwable $e) {
            $pemasukan = collect();
            $pengeluaran = collect();
        }

        $records = $pemasukan->concat($pengeluaran)
            ->sortBy('created_at')
            ->values();

        return compact('records', 'pemasukan', 'pengeluaran', 'month', 'category');
    }
}
