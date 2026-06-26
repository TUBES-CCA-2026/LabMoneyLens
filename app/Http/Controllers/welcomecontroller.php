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

        $jenis = DB::table('jenis_pengeluaran')->select('id_jenis_pengeluaran as id', 'nama_jenis as nama')->get();

        return view('welcome', compact('expenses', 'jenis'));
    }


    public function store(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'tanggal' => 'required|date',
            'uraian' => 'nullable|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'id_jenis_pengeluaran' => 'required|integer',
            'receipt_image' => 'nullable|image|max:5120',
        ]);

        // Validasi saldo tidak boleh Rp0
        $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->sum('nominal');
        $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->sum('nominal') + $data['nominal'];
        $newBalance = $totalIncome - $totalExpense;
        
        if ($newBalance == 0) {
            return redirect()->route('welcome')->with('error', 'Saldo tidak boleh Rp0. Operasi dibatalkan.');
        }

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
        }

        DB::table('pengeluaran')->insert([
            'tanggal' => $data['tanggal'],
            'uraian' => $data['uraian'] ?? '',
            'nominal' => $data['nominal'],
            'foto_struk' => $receiptPath,
            'id_jenis_pengeluaran' => $data['id_jenis_pengeluaran'],
            'id_user' => session('user_id'),
            'is_confirmed' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('welcome')->with('success', 'Pengeluaran disimpan.');
    }


    public function destroy($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        DB::table('pengeluaran')
            ->where('id_pengeluaran', $id)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);

        return redirect()->route('welcome')->with('success', 'Pengeluaran dihapus.');
    }

    public function edit($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $expense = DB::table('pengeluaran')
            ->join('jenis_pengeluaran', 'pengeluaran.id_jenis_pengeluaran', '=', 'jenis_pengeluaran.id_jenis_pengeluaran')
            ->select(
                'pengeluaran.id_pengeluaran as id',
                'jenis_pengeluaran.nama_jenis as kategori',
                'pengeluaran.nominal as jumlah',
                'pengeluaran.tanggal as tanggal',
                'pengeluaran.id_jenis_pengeluaran as id_jenis_pengeluaran',
                'pengeluaran.is_confirmed as is_confirmed'
            )
            ->where('pengeluaran.id_pengeluaran', $id)
            ->whereNull('pengeluaran.deleted_at')
            ->first();

        if (!$expense) {
            return redirect()->route('welcome')->with('error', 'Pengeluaran tidak ditemukan.');
        }

        $jenis = DB::table('jenis_pengeluaran')->select('id_jenis_pengeluaran as id', 'nama_jenis as nama')->get();

        return view('pengeluaran_edit', compact('expense', 'jenis'));
    }

    public function update(Request $request, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'tanggal' => 'required|date',
            'nominal' => 'required|numeric|min:0',
            'id_jenis_pengeluaran' => 'required|integer',
        ]);

        $updated = DB::table('pengeluaran')
            ->where('id_pengeluaran', $id)
            ->whereNull('deleted_at')
            ->update([
                'tanggal' => $data['tanggal'],
                'nominal' => $data['nominal'],
                'id_jenis_pengeluaran' => $data['id_jenis_pengeluaran'],
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return redirect()->route('welcome')->with('error', 'Pengeluaran tidak dapat diperbarui.');
        }

        return redirect()->route('welcome')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

}
