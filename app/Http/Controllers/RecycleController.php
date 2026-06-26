<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecycleController extends Controller
{
    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
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

        $records = $deletedIncome->concat($deletedExpense)
            ->sortByDesc('deleted_at')
            ->values();

        $totalItems = $records->count();
        $totalValue = $records->sum('jumlah');

        return view('recycle', compact('records', 'totalItems', 'totalValue'));
    }

    public function restore(Request $request, $type, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
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

        DB::table('pemasukan')->whereNotNull('deleted_at')->update(['deleted_at' => null]);
        DB::table('pengeluaran')->whereNotNull('deleted_at')->update(['deleted_at' => null]);

        return redirect()->route('recycle')->with('success', 'Semua item berhasil dipulihkan.');
    }

    public function emptyTrash(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        DB::table('pemasukan')->whereNotNull('deleted_at')->delete();
        DB::table('pengeluaran')->whereNotNull('deleted_at')->delete();

        return redirect()->route('recycle')->with('success', 'Sampah berhasil dikosongkan.');
    }
}
