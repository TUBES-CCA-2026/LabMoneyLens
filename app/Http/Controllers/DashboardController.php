<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $totalIncome = DB::table('pemasukan')->sum('nominal');
        $totalExpense = DB::table('pengeluaran')->sum('nominal');
        $balance = $totalIncome - $totalExpense;

        $expenseCategories = DB::table('jenis_pengeluaran')
            ->leftJoin('pengeluaran', 'jenis_pengeluaran.id_jenis_pengeluaran', '=', 'pengeluaran.id_jenis_pengeluaran')
            ->select('jenis_pengeluaran.nama_jenis as category', DB::raw('COALESCE(SUM(pengeluaran.nominal), 0) as total'))
            ->groupBy('jenis_pengeluaran.nama_jenis')
            ->orderByDesc('total')
            ->get();

        $recentIncome = DB::table('pemasukan')
            ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
            ->select(
                'pemasukan.id_pemasukan as id',
                'jenis_penerimaan.nama_jenis as category',
                'pemasukan.nominal as amount',
                'pemasukan.tanggal as tanggal',
                DB::raw("'Pemasukan' as type")
            )
            ->get();

        $recentExpense = DB::table('pengeluaran')
            ->join('jenis_pengeluaran', 'pengeluaran.id_jenis_pengeluaran', '=', 'jenis_pengeluaran.id_jenis_pengeluaran')
            ->select(
                'pengeluaran.id_pengeluaran as id',
                'jenis_pengeluaran.nama_jenis as category',
                'pengeluaran.nominal as amount',
                'pengeluaran.tanggal as tanggal',
                DB::raw("'Pengeluaran' as type")
            )
            ->get();

        $recentTransactions = $recentIncome
            ->concat($recentExpense)
            ->sortByDesc('tanggal')
            ->take(6);

        return view('dashboard', compact(
            'totalIncome',
            'totalExpense',
            'balance',
            'expenseCategories',
            'recentTransactions'
        ));
    }
}
