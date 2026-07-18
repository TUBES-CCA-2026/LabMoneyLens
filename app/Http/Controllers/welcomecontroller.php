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

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
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

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
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

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        DB::table('pengeluaran')
            ->where('id_pengeluaran', $id)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);

        // Jika dihapus dari halaman laporan, kembali ke laporan
        $referer = request()->headers->get('referer', '');
        if (str_contains($referer, '/laporan')) {
            return redirect()->route('laporan')->with('success', 'Pengeluaran dihapus.');
        }

        return redirect()->route('welcome')->with('success', 'Pengeluaran dihapus.');
    }

    public function edit($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        $baseItem = DB::table('pengeluaran')->where('id_pengeluaran', $id)->first();
        if (!$baseItem) {
            return redirect()->route('welcome')->with('error', 'Pengeluaran tidak ditemukan.');
        }

        $expenses = DB::table('pengeluaran')
            ->join('jenis_pengeluaran', 'pengeluaran.id_jenis_pengeluaran', '=', 'jenis_pengeluaran.id_jenis_pengeluaran')
            ->select(
                'pengeluaran.id_pengeluaran as id',
                'jenis_pengeluaran.nama_jenis as kategori',
                'pengeluaran.nominal as jumlah',
                'pengeluaran.tanggal as tanggal',
                'pengeluaran.uraian as uraian',
                'pengeluaran.foto_struk as foto_struk',
                'pengeluaran.id_jenis_pengeluaran as id_jenis_pengeluaran',
                'pengeluaran.is_confirmed as is_confirmed'
            )
            ->where('pengeluaran.created_at', $baseItem->created_at)
            ->whereNull('pengeluaran.deleted_at')
            ->get();

        $expense = $expenses->first();

        $jenis = DB::table('jenis_pengeluaran')->select('id_jenis_pengeluaran as id', 'nama_jenis as nama')->get();

        return view('pengeluaran_edit', compact('expense', 'expenses', 'jenis'));
    }

    public function update(Request $request, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'tanggal' => 'required|date',
            'id_pengeluaran' => 'array',
            'uraian' => 'array',
            'uraian.*' => 'nullable|string|max:255',
            'nominal' => 'required|array',
            'nominal.*' => 'required|numeric|min:0',
            'id_jenis_pengeluaran' => 'required|integer',
        ]);

        $baseItem = DB::table('pengeluaran')->where('id_pengeluaran', $id)->first();
        if (!$baseItem) return redirect()->route('welcome')->with('error', 'Pengeluaran tidak ditemukan.');

        $groupIds = DB::table('pengeluaran')
            ->where('created_at', $baseItem->created_at)
            ->whereNull('deleted_at')
            ->pluck('id_pengeluaran')->toArray();

        $submittedIds = $request->input('id_pengeluaran', []);
        
        $deletedIds = array_diff($groupIds, $submittedIds);
        if (!empty($deletedIds)) {
            DB::table('pengeluaran')->whereIn('id_pengeluaran', $deletedIds)->update(['deleted_at' => now()]);
        }

        // Validasi pengeluaran tidak boleh melebihi saldo yang dimiliki
        $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $totalExpenseOther = DB::table('pengeluaran')
            ->where('is_confirmed', 1)
            ->whereNull('deleted_at')
            ->whereNotIn('id_pengeluaran', $groupIds)
            ->sum('nominal');
            
        $currentBalance = $totalIncome - $totalExpenseOther;
        $newTotalNominal = array_sum($data['nominal']);

        if ($newTotalNominal > $currentBalance) {
            return redirect()->route('pengeluaran.edit', $id)->with('error', 'Pengeluaran tidak boleh melebihi saldo. Saldo tersedia: Rp ' . number_format($currentBalance, 0, ',', '.'));
        }

        for ($i = 0; $i < count($data['nominal']); $i++) {
            $itemId = $submittedIds[$i] ?? null;
            $nominal = $data['nominal'][$i];
            $uraian = $data['uraian'][$i] ?? '';

            if ($itemId && in_array($itemId, $groupIds)) {
                DB::table('pengeluaran')->where('id_pengeluaran', $itemId)->update([
                    'tanggal' => $data['tanggal'],
                    'uraian' => $uraian,
                    'nominal' => $nominal,
                    'id_jenis_pengeluaran' => $data['id_jenis_pengeluaran'],
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('pengeluaran')->insert([
                    'tanggal' => $data['tanggal'],
                    'uraian' => $uraian,
                    'nominal' => $nominal,
                    'foto_struk' => $baseItem->foto_struk,
                    'id_jenis_pengeluaran' => $data['id_jenis_pengeluaran'],
                    'id_user' => session('user_id'),
                    'is_confirmed' => 1,
                    'created_at' => $baseItem->created_at,
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('welcome')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

}
