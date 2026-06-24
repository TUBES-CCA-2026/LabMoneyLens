<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $month = $request->query('month');
        $category = $request->query('category');

        $pemasukanQuery = DB::table('pemasukan')
            ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
            ->select(
                'pemasukan.id_pemasukan as id',
                'jenis_penerimaan.nama_jenis as kategori',
                'pemasukan.nominal as jumlah',
                'pemasukan.tanggal',
                DB::raw("'Pemasukan' as tipe")
            )
            ->whereNull('pemasukan.deleted_at');

        $pengeluaranQuery = DB::table('pengeluaran')
            ->join('jenis_pengeluaran', 'pengeluaran.id_jenis_pengeluaran', '=', 'jenis_pengeluaran.id_jenis_pengeluaran')
            ->select(
                'pengeluaran.id_pengeluaran as id',
                'jenis_pengeluaran.nama_jenis as kategori',
                'pengeluaran.nominal as jumlah',
                'pengeluaran.tanggal',
                DB::raw("'Pengeluaran' as tipe")
            )
            ->whereNull('pengeluaran.deleted_at');

        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            [$year, $monthNumber] = explode('-', $month);
            $pemasukanQuery->whereYear('pemasukan.tanggal', $year)->whereMonth('pemasukan.tanggal', $monthNumber);
            $pengeluaranQuery->whereYear('pengeluaran.tanggal', $year)->whereMonth('pengeluaran.tanggal', $monthNumber);
        }

        if ($category && $category !== 'semua') {
            $pemasukanQuery->where('jenis_penerimaan.nama_jenis', $category);
            $pengeluaranQuery->where('jenis_pengeluaran.nama_jenis', $category);
        }

        $pemasukan = $pemasukanQuery->get();
        $pengeluaran = $pengeluaranQuery->get();

        $records = $pemasukan->concat($pengeluaran)
            ->sortByDesc('tanggal')
            ->values();

        $totalIncome = $pemasukan->sum('jumlah');
        $totalExpense = $pengeluaran->sum('jumlah');
        $balance = $totalIncome - $totalExpense;

        $categories = DB::table('jenis_penerimaan')->pluck('nama_jenis')
            ->concat(DB::table('jenis_pengeluaran')->pluck('nama_jenis'))
            ->unique()
            ->values();
        $categories->prepend('Semua');

        return view('laporan', compact(
            'records',
            'totalIncome',
            'totalExpense',
            'balance',
            'categories',
            'month',
            'category'
        ));
    }
}
