<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemasukanController extends Controller
{
    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $incomes = DB::table('pemasukan')
            ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
            ->select(
                'pemasukan.id_pemasukan as id',
                'jenis_penerimaan.nama_jenis as kategori',
                'pemasukan.nominal as jumlah',
                'pemasukan.tanggal as tanggal',
                'pemasukan.uraian as uraian',
                'pemasukan.created_at as created_at'
            )
            ->whereNull('pemasukan.deleted_at')
            ->orderBy('pemasukan.tanggal', 'desc')
            ->get();

        $jenis = DB::table('jenis_penerimaan')->select('id_jenis_penerimaan as id', 'nama_jenis as nama')->get();

        return view('pemasukan', compact('incomes', 'jenis'));
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
            'nominal.*' => 'required|numeric|min:0',
            'id_jenis_penerimaan' => 'required|array',
            'id_jenis_penerimaan.*' => 'required|integer',
            'receipt_image' => 'required|image|max:5120',
        ]);

        $totalNominal = array_sum($data['nominal']);

        // Validasi saldo tidak boleh Rp0
        $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal') + $totalNominal;
        $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $newBalance = $totalIncome - $totalExpense;
        
        if ($newBalance == 0) {
            return redirect()->route('pemasukan')->with('error', 'Saldo tidak boleh Rp0. Operasi dibatalkan.');
        }

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
        }

        $itemsCount = count($data['nominal']);
        
        for ($i = 0; $i < $itemsCount; $i++) {
            $nominal = $data['nominal'][$i];
            $uraian = $data['uraian'][$i] ?? '';
            $id_jenis = $data['id_jenis_penerimaan'][$i];

            // Cek apakah entri serupa ada di Recycle Bin (deleted)
            $existsInRecycle = DB::table('pemasukan')
                ->whereNotNull('deleted_at')
                ->where('nominal', $nominal)
                ->where('tanggal', $data['tanggal'])
                ->where('id_jenis_penerimaan', $id_jenis)
                ->exists();

            if ($existsInRecycle) {
                return redirect()->route('pemasukan')->with('error', "Entri serupa (Rp " . number_format($nominal, 0, ',', '.') . ") ditemukan di Recycle Bin. Pulihkan entri tersebut sebelum menambahkan kembali.");
            }

            DB::table('pemasukan')->insert([
                'tanggal' => $data['tanggal'],
                'uraian' => $uraian,
                'nominal' => $nominal,
                'foto_bukti' => $receiptPath,
                'id_jenis_penerimaan' => $id_jenis,
                'id_user' => session('user_id'),
                'is_confirmed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('pemasukan')->with('success', 'Semua pemasukan berhasil disimpan.');
    }

    public function edit($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $baseItem = DB::table('pemasukan')->where('id_pemasukan', $id)->first();
        if (!$baseItem) {
            return redirect()->route('pemasukan')->with('error', 'Pemasukan tidak ditemukan.');
        }

        $incomes = DB::table('pemasukan')
            ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
            ->select(
                'pemasukan.id_pemasukan as id',
                'jenis_penerimaan.nama_jenis as kategori',
                'pemasukan.nominal as jumlah',
                'pemasukan.tanggal as tanggal',
                'pemasukan.uraian as uraian',
                'pemasukan.foto_bukti as foto_struk',
                'pemasukan.id_jenis_penerimaan as id_jenis_penerimaan'
            )
            ->where('pemasukan.created_at', $baseItem->created_at)
            ->whereNull('pemasukan.deleted_at')
            ->get();

        $income = $incomes->first();

        $jenis = DB::table('jenis_penerimaan')->select('id_jenis_penerimaan as id', 'nama_jenis as nama')->get();

        return view('pemasukan_edit', compact('income', 'incomes', 'jenis'));
    }

    public function update(Request $request, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'tanggal' => 'required|date',
            'id_pemasukan' => 'array',
            'uraian' => 'array',
            'uraian.*' => 'nullable|string|max:255',
            'nominal' => 'required|array',
            'nominal.*' => 'required|numeric|min:0',
            'id_jenis_penerimaan' => 'required|integer',
        ]);

        $baseItem = DB::table('pemasukan')->where('id_pemasukan', $id)->first();
        if (!$baseItem) return redirect()->route('pemasukan')->with('error', 'Pemasukan tidak ditemukan.');

        $groupIds = DB::table('pemasukan')
            ->where('created_at', $baseItem->created_at)
            ->whereNull('deleted_at')
            ->pluck('id_pemasukan')->toArray();

        $submittedIds = $request->input('id_pemasukan', []);
        
        $deletedIds = array_diff($groupIds, $submittedIds);
        if (!empty($deletedIds)) {
            DB::table('pemasukan')->whereIn('id_pemasukan', $deletedIds)->update(['deleted_at' => now()]);
        }

        for ($i = 0; $i < count($data['nominal']); $i++) {
            $itemId = $submittedIds[$i] ?? null;
            $nominal = $data['nominal'][$i];
            $uraian = $data['uraian'][$i] ?? '';

            if ($itemId && in_array($itemId, $groupIds)) {
                DB::table('pemasukan')->where('id_pemasukan', $itemId)->update([
                    'tanggal' => $data['tanggal'],
                    'uraian' => $uraian,
                    'nominal' => $nominal,
                    'id_jenis_penerimaan' => $data['id_jenis_penerimaan'],
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('pemasukan')->insert([
                    'tanggal' => $data['tanggal'],
                    'uraian' => $uraian,
                    'nominal' => $nominal,
                    'foto_bukti' => $baseItem->foto_bukti,
                    'id_jenis_penerimaan' => $data['id_jenis_penerimaan'],
                    'id_user' => session('user_id'),
                    'is_confirmed' => 1,
                    'created_at' => $baseItem->created_at,
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('pemasukan')->with('success', 'Pemasukan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        DB::table('pemasukan')
            ->where('id_pemasukan', $id)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);

        // Jika dihapus dari halaman laporan, kembali ke laporan
        $referer = request()->headers->get('referer', '');
        if (str_contains($referer, '/laporan')) {
            return redirect()->route('laporan')->with('success', 'Pemasukan dihapus.');
        }

        return redirect()->route('pemasukan')->with('success', 'Pemasukan dihapus.');
    }
}
