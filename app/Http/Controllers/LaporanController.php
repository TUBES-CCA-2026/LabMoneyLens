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

        $query = $this->buildReportQuery($request);
        $records = $query['records'];
        $pemasukan = $query['pemasukan'];
        $pengeluaran = $query['pengeluaran'];
        $month = $query['month'];
        $category = $query['category'];

        $groupedRecords = $records->groupBy(function($item) {
            return $item->created_at . '|' . $item->kategori . '|' . $item->tipe;
        });

        $recentRecords = $records->sortByDesc('created_at')->values()->take(10);

        if ($request->query('export') === 'csv') {
            if ($records->isEmpty()) {
                return redirect()->route('laporan', array_filter([
                    'month' => $month,
                    'category' => $category,
                ], fn ($value) => $value !== null && $value !== ''))
                    ->with('error', 'Tidak ada data laporan untuk diunduh.');
            }

            $filename = 'laporan-' . now()->format('YmdHis') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($records) {
                $output = fopen('php://output', 'w');
                fprintf($output, "%s", chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($output, ['ID Laporan', 'Kategori', 'Uraian', 'Jumlah (IDR)', 'Tanggal & Waktu', 'Jenis']);

                foreach ($records as $row) {
                    fputcsv($output, [
                        $row->id,
                        $row->kategori,
                        $row->uraian ?? '-',
                        $row->jumlah,
                        \Illuminate\Support\Carbon::parse($row->created_at)->format('d/m/Y H:i'),
                        $row->tipe,
                    ]);
                }

                fclose($output);
            };

            return response()->stream($callback, 200, $headers);
        }

        $totalIncome = $pemasukan->sum('jumlah');
        $totalExpense = $pengeluaran->sum('jumlah');
        $balance = $totalIncome - $totalExpense;

        $categories = DB::table('jenis_penerimaan')->pluck('nama_jenis')
            ->concat(DB::table('jenis_pengeluaran')->pluck('nama_jenis'))
            ->unique()
            ->values();
        $categories->prepend('Semua');

        return view('laporan', compact(
            'groupedRecords',
            'records',
            'recentRecords',
            'totalIncome',
            'totalExpense',
            'balance',
            'categories',
            'month',
            'category'
        ));
    }

    public function liveData(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = $this->buildReportQuery($request);
        $records = $query['records'];
        $pemasukan = $query['pemasukan'];
        $pengeluaran = $query['pengeluaran'];

        $groupedRecords = $records->groupBy(function($item) {
            return $item->created_at . '|' . $item->kategori . '|' . $item->tipe;
        });

        $formattedGroups = [];
        foreach ($groupedRecords as $group) {
            $first = $group->first();
            $formattedGroups[] = [
                'kategori' => $first->kategori,
                'tipe' => $first->tipe,
                'created_at' => \Illuminate\Support\Carbon::parse($first->created_at)->toIso8601String(),
                'total' => (float) $group->sum('jumlah'),
                'items' => $group->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'uraian' => $row->uraian,
                        'jumlah' => (float) $row->jumlah,
                    ];
                })->values()
            ];
        }

        return response()->json([
            'totalIncome' => (float) $pemasukan->sum('jumlah'),
            'totalExpense' => (float) $pengeluaran->sum('jumlah'),
            'balance' => (float) ($pemasukan->sum('jumlah') - $pengeluaran->sum('jumlah')),
            'groupedRecords' => $formattedGroups,
        ]);
    }

    private function buildReportQuery(Request $request): array
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
                'pemasukan.uraian',
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
                'pengeluaran.created_at as created_at',
                'pengeluaran.uraian',
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
            ->sortBy('created_at')
            ->values();

        return compact('records', 'pemasukan', 'pengeluaran', 'month', 'category');
    }
}
