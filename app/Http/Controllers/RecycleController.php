<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class RecycleController extends Controller
{
    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        $deletedIncome = DB::table('pemasukan')
            ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
            ->select(
                'pemasukan.id_pemasukan as id',
                'jenis_penerimaan.nama_jenis as kategori',
                'pemasukan.nominal as jumlah',
                'pemasukan.tanggal as tanggal',
                'pemasukan.deleted_at as deleted_at',
                DB::raw("'Pemasukan' as tipe")
            )
            ->whereNotNull('pemasukan.deleted_at')
            ->get();

        $deletedExpense = DB::table('pengeluaran')
            ->join('jenis_pengeluaran', 'pengeluaran.id_jenis_pengeluaran', '=', 'jenis_pengeluaran.id_jenis_pengeluaran')
            ->select(
                'pengeluaran.id_pengeluaran as id',
                'jenis_pengeluaran.nama_jenis as kategori',
                'pengeluaran.nominal as jumlah',
                'pengeluaran.tanggal as tanggal',
                'pengeluaran.deleted_at as deleted_at',
                DB::raw("'Pengeluaran' as tipe")
            )
            ->whereNotNull('pengeluaran.deleted_at')
            ->get();

        $recordsCollection = $deletedIncome->concat($deletedExpense)
            ->sortByDesc('deleted_at')
            ->values();

        $totalItems = $recordsCollection->count();
        $totalValue = $recordsCollection->sum('jumlah');

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $records = new LengthAwarePaginator(
            $recordsCollection->forPage($currentPage, $perPage),
            $totalItems,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('recycle', compact('records', 'totalItems', 'totalValue'));
    }

    public function restore(Request $request, $type, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        if ($type === 'pemasukan') {
            DB::table('pemasukan')
                ->where('id_pemasukan', $id)
                ->whereNotNull('deleted_at')
                ->update(['deleted_at' => null]);
        } elseif ($type === 'pengeluaran') {
            DB::table('pengeluaran')
                ->where('id_pengeluaran', $id)
                ->whereNotNull('deleted_at')
                ->update(['deleted_at' => null]);
        }

        return redirect()->route('recycle')->with('success', 'Item berhasil dipulihkan.');
    }

    public function forceDelete(Request $request, $type, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        if ($type === 'pemasukan') {
            DB::table('pemasukan')
                ->where('id_pemasukan', $id)
                ->whereNotNull('deleted_at')
                ->delete();
        } elseif ($type === 'pengeluaran') {
            DB::table('pengeluaran')
                ->where('id_pengeluaran', $id)
                ->whereNotNull('deleted_at')
                ->delete();
        }

        return redirect()->route('recycle')->with('success', 'Item berhasil dihapus permanen.');
    }

    public function restoreAll(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        DB::table('pemasukan')->whereNotNull('deleted_at')->update(['deleted_at' => null]);
        DB::table('pengeluaran')->whereNotNull('deleted_at')->update(['deleted_at' => null]);

        return redirect()->route('recycle')->with('success', 'Semua item berhasil dipulihkan.');
    }

    public function emptyTrash(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        DB::table('pemasukan')->whereNotNull('deleted_at')->delete();
        DB::table('pengeluaran')->whereNotNull('deleted_at')->delete();

        return redirect()->route('recycle')->with('success', 'Sampah berhasil dikosongkan.');
    }
}
