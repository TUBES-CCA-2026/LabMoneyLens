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
                'pemasukan.tanggal as tanggal'
            )
            ->whereNull('pemasukan.deleted_at')
            ->orderByDesc('pemasukan.tanggal')
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
            'uraian' => 'nullable|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'id_jenis_penerimaan' => 'required|integer',
            'receipt_image' => 'nullable|image|max:5120',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
        }

        DB::table('pemasukan')->insert([
            'tanggal' => $data['tanggal'],
            'uraian' => $data['uraian'] ?? '',
            'nominal' => $data['nominal'],
            'foto_bukti' => $receiptPath,
            'id_jenis_penerimaan' => $data['id_jenis_penerimaan'],
            'id_user' => session('user_id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('pemasukan')->with('success', 'Pemasukan disimpan.');
    }

    public function edit($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $income = DB::table('pemasukan')
            ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
            ->select(
                'pemasukan.id_pemasukan as id',
                'jenis_penerimaan.nama_jenis as kategori',
                'pemasukan.nominal as jumlah',
                'pemasukan.tanggal as tanggal',
                'pemasukan.uraian as uraian',
                'pemasukan.id_jenis_penerimaan as id_jenis_penerimaan'
            )
            ->where('pemasukan.id_pemasukan', $id)
            ->whereNull('pemasukan.deleted_at')
            ->first();

        if (!$income) {
            return redirect()->route('pemasukan')->with('error', 'Pemasukan tidak ditemukan.');
        }

        $jenis = DB::table('jenis_penerimaan')->select('id_jenis_penerimaan as id', 'nama_jenis as nama')->get();

        return view('pemasukan_edit', compact('income', 'jenis'));
    }

    public function update(Request $request, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'tanggal' => 'required|date',
            'uraian' => 'nullable|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'id_jenis_penerimaan' => 'required|integer',
        ]);

        $updated = DB::table('pemasukan')
            ->where('id_pemasukan', $id)
            ->whereNull('deleted_at')
            ->update([
                'tanggal' => $data['tanggal'],
                'uraian' => $data['uraian'] ?? '',
                'nominal' => $data['nominal'],
                'id_jenis_penerimaan' => $data['id_jenis_penerimaan'],
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return redirect()->route('pemasukan')->with('error', 'Pemasukan tidak dapat diperbarui.');
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

        return redirect()->route('pemasukan')->with('success', 'Pemasukan dihapus.');
    }
}
