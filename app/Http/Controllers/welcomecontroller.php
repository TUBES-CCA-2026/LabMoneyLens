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
                'pengeluaran.tanggal as tanggal',
                'pengeluaran.uraian as uraian',
                'pengeluaran.created_at as created_at'
            )
            ->whereNull('pengeluaran.deleted_at')
            ->orderBy('pengeluaran.tanggal', 'desc')
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
            'uraian' => 'array',
            'uraian.*' => 'nullable|string|max:255',
            'nominal' => 'required|array',
            'nominal.*' => 'required|numeric|min:1',
            'id_jenis_pengeluaran' => 'required|array',
            'id_jenis_pengeluaran.*' => 'required|integer',
            'receipt_image' => 'required|image|max:5120',
        ]);

        $totalNominal = array_sum($data['nominal']);

        // Validasi pengeluaran tidak boleh melebihi saldo yang dimiliki
        $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $currentBalance = $totalIncome - $totalExpense;
        
        if ($totalNominal > $currentBalance) {
            return redirect()->route('welcome')->with('error', 'Total pengeluaran tidak boleh melebihi saldo. Saldo Anda: Rp ' . number_format($currentBalance, 0, ',', '.'));
        }

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
        }

        $itemsCount = count($data['nominal']);
        for ($i = 0; $i < $itemsCount; $i++) {
            $nominal = $data['nominal'][$i];
            $uraian = $data['uraian'][$i] ?? '';
            $id_jenis = $data['id_jenis_pengeluaran'][$i];

            // Cek apakah entri serupa ada di Recycle Bin (deleted)
            $existsInRecycle = DB::table('pengeluaran')
                ->whereNotNull('deleted_at')
                ->where('nominal', $nominal)
                ->where('tanggal', $data['tanggal'])
                ->where('id_jenis_pengeluaran', $id_jenis)
                ->exists();

            if ($existsInRecycle) {
                return redirect()->route('welcome')->with('error', "Entri serupa (Rp " . number_format($nominal, 0, ',', '.') . ") ditemukan di Recycle Bin. Pulihkan entri tersebut sebelum menambahkan kembali.");
            }

            DB::table('pengeluaran')->insert([
                'tanggal' => $data['tanggal'],
                'uraian' => $uraian,
                'nominal' => $nominal,
                'foto_struk' => $receiptPath,
                'id_jenis_pengeluaran' => $id_jenis,
                'id_user' => session('user_id'),
                'is_confirmed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('welcome')->with('success', 'Semua pengeluaran berhasil disimpan.');
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

        // Get old expense amount
        $oldExpense = DB::table('pengeluaran')
            ->where('id_pengeluaran', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$oldExpense) {
            return redirect()->route('welcome')->with('error', 'Pengeluaran tidak ditemukan.');
        }

        // Validasi pengeluaran tidak boleh melebihi saldo yang dimiliki
        $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->sum('nominal');
        $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->sum('nominal');
        $currentBalance = $totalIncome - $totalExpense;
        $maxAllowedExpense = $currentBalance + $oldExpense->nominal; // Saldo + pengeluaran lama

        if ($data['nominal'] > $maxAllowedExpense) {
            return redirect()->route('pengeluaran.edit', $id)->with('error', 'Pengeluaran tidak boleh melebihi saldo. Saldo tersedia: Rp ' . number_format($maxAllowedExpense, 0, ',', '.'));
        }

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
