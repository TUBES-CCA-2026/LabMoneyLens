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
                'pemasukan.is_confirmed as is_confirmed'
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
            'is_confirmed' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('pemasukan')->with('success', 'Pemasukan disimpan (menunggu konfirmasi).');
    }

    public function confirm($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        DB::table('pemasukan')
            ->where('id_pemasukan', $id)
            ->whereNull('deleted_at')
            ->update(['is_confirmed' => true, 'updated_at' => now()]);

        return redirect()->route('pemasukan')->with('success', 'Pemasukan dikonfirmasi.');
    }

    public function confirmAll()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        DB::table('pemasukan')
            ->whereNull('deleted_at')
            ->where('is_confirmed', false)
            ->update(['is_confirmed' => true, 'updated_at' => now()]);

        return redirect()->route('pemasukan')->with('success', 'Semua pemasukan terkonfirmasi dan masuk ke laporan.');
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
