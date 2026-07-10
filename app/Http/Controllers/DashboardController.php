<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Mendapatkan range tanggal berdasarkan semester.
     * Sem 1 = Januari – Juni, Sem 2 = Juli – Desember.
     */
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

    public function index(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        // Tentukan semester & tahun yang aktif
        $currentYear     = (int) now()->year;
        $currentSemester = now()->month <= 6 ? 1 : 2;
        $selectedYear     = (int) $request->query('year', $currentYear);
        $selectedSemester = (int) $request->query('semester', $currentSemester);

        // Hitung semua tahun yang punya data (untuk dropdown)
        $availableYears = collect([
            DB::table('pemasukan')->whereNull('deleted_at')->selectRaw('YEAR(tanggal) as yr')->distinct()->pluck('yr'),
            DB::table('pengeluaran')->whereNull('deleted_at')->selectRaw('YEAR(tanggal) as yr')->distinct()->pluck('yr'),
        ])->flatten()->unique()->sort()->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([$currentYear]);
        }

        // Range semester yang dipilih
        $range = $this->getSemesterRange($selectedYear, $selectedSemester);

        $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $balance = $totalIncome - $totalExpense;

        // Chart difilter per semester yang dipilih
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

        return view('dashboard', compact(
            'totalIncome',
            'totalExpense',
            'balance',
            'expenseCategories',
            'recentTransactions',
            'selectedYear',
            'selectedSemester',
            'availableYears'
        ));
    }

    /**
     * AJAX endpoint: kembalikan data chart pengeluaran per semester.
     */
    public function chartData(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

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

        return response()->json([
            'categories' => $expenseCategories,
            'year'       => $year,
            'semester'   => $semester,
        ]);
    }
}
