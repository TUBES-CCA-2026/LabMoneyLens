<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class welcomecontroller extends Controller
{
    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $expenses = DB::table('pengeluaran')
            ->join('jenis_pengeluaran', 'pengeluaran.id_jenis_pengeluaran', '=', 'jenis_pengeluaran.id_jenis_pengeluaran')
            ->select(
                'pengeluaran.id_pengeluaran as id',
                'jenis_pengeluaran.nama_jenis as kategori',
                'pengeluaran.nominal as jumlah',
                'pengeluaran.tanggal as tanggal'
            )
            ->whereNull('pengeluaran.deleted_at')
            ->orderByDesc('pengeluaran.tanggal')
            ->get();

        return view('welcome', compact('expenses'));
    }
}
