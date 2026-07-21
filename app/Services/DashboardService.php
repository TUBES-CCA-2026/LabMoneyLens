<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    private function getSemesterRange(int $year, int $semester): array
    {
        if ($semester === 1) {
            return [
                'start' => Carbon::create($year, 1, 1)->startOfDay(),
                'end'   => Carbon::create($year, 6, 30)->endOfDay(),
            ];
        }
        return [
            'start' => Carbon::create($year, 7, 1)->startOfDay(),
            'end'   => Carbon::create($year, 12, 31)->endOfDay(),
        ];
    }

    public function getDashboardData(\Illuminate\Http\Request $request)
    {
        $currentYear     = (int) now()->year;
        $currentSemester = now()->month <= 6 ? 1 : 2;
        $selectedYear     = (int) $request->query('year', $currentYear);
        $selectedSemester = (int) $request->query('semester', $currentSemester);

        try {
            $availableYears = collect([
                DB::table('pemasukan')->whereNull('deleted_at')->selectRaw('YEAR(tanggal) as yr')->distinct()->pluck('yr'),
                DB::table('pengeluaran')->whereNull('deleted_at')->selectRaw('YEAR(tanggal) as yr')->distinct()->pluck('yr'),
            ])->flatten()->unique()->sort()->values();

            if ($availableYears->isEmpty()) {
                $availableYears = collect([$currentYear]);
            }

            $range = $this->getSemesterRange($selectedYear, $selectedSemester);

            $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
            $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
            $balance = $totalIncome - $totalExpense;

            $expenseCategories = DB::table('jenis_pengeluaran')
                ->leftJoin('pengeluaran', function ($join) use ($range) {
                    $join->on('jenis_pengeluaran.id_jenis_pengeluaran', '=', 'pengeluaran.id_jenis_pengeluaran')
                         ->whereNull('pengeluaran.deleted_at')
                         ->whereBetween('pengeluaran.tanggal', [$range['start'], $range['end']]);
                })
                ->select('jenis_pengeluaran.nama_jenis as category', DB::raw('COALESCE(SUM(pengeluaran.nominal), 0) as total'))
                ->groupBy('jenis_pengeluaran.nama_jenis')
                ->orderByDesc('total')
                ->get();

            $recentIncome = DB::table('pemasukan')
                ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
                ->where('pemasukan.is_confirmed', 1)
                ->whereNull('pemasukan.deleted_at')
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
                ->where('pengeluaran.is_confirmed', 1)
                ->whereNull('pengeluaran.deleted_at')
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
        } catch (\Throwable $e) {
            $availableYears = collect([$currentYear]);
            $totalIncome = 0;
            $totalExpense = 0;
            $balance = 0;
            $expenseCategories = collect();
            $recentTransactions = collect();
        }

        return compact(
            'totalIncome',
            'totalExpense',
            'balance',
            'expenseCategories',
            'recentTransactions',
            'selectedYear',
            'selectedSemester',
            'availableYears'
        );
    }

    public function chartData(\Illuminate\Http\Request $request)
    {
        try {
            $year     = (int) $request->query('year', now()->year);
            $semester = (int) $request->query('semester', now()->month <= 6 ? 1 : 2);
            $range    = $this->getSemesterRange($year, $semester);

            $expenseCategories = DB::table('jenis_pengeluaran')
                ->leftJoin('pengeluaran', function ($join) use ($range) {
                    $join->on('jenis_pengeluaran.id_jenis_pengeluaran', '=', 'pengeluaran.id_jenis_pengeluaran')
                         ->whereNull('pengeluaran.deleted_at')
                         ->whereBetween('pengeluaran.tanggal', [$range['start'], $range['end']]);
                })
                ->select('jenis_pengeluaran.nama_jenis as category', DB::raw('COALESCE(SUM(pengeluaran.nominal), 0) as total'))
                ->groupBy('jenis_pengeluaran.nama_jenis')
                ->orderByDesc('total')
                ->get();

            return compact('expenseCategories', 'year', 'semester');
        } catch (\Throwable $e) {
            return compact('expenseCategories', 'year', 'semester');
        }
    }

    public function liveData()
    {
        try {
            $recentIncome = DB::table('pemasukan')
                ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
                ->where('pemasukan.is_confirmed', 1)
                ->whereNull('pemasukan.deleted_at')
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
                ->where('pengeluaran.is_confirmed', 1)
                ->whereNull('pengeluaran.deleted_at')
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
                ->take(6)
                ->values();

            $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
            $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
            $balance = $totalIncome - $totalExpense;
        } catch (\Throwable $e) {
            $recentTransactions = collect();
            $totalIncome = 0;
            $totalExpense = 0;
            $balance = 0;
        }

        return compact('totalIncome', 'totalExpense', 'balance', 'recentTransactions');
    }
}
