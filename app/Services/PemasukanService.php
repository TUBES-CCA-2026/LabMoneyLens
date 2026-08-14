<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class PemasukanService
{
    public function store(array $data, ?UploadedFile $receiptImage = null)
    {
        Log::debug('PemasukanService::store received nominal', ['nominal' => $data['nominal'] ?? null]);
        if (isset($data['nominal']) && is_array($data['nominal'])) {
            foreach ($data['nominal'] as $i => $val) {
                Log::debug('PemasukanService::store nominal item', ['index' => $i, 'raw' => $val]);
            }
        }
        $totalNominal = array_sum($data['nominal']);

        $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal') + $totalNominal;
        $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $newBalance = $totalIncome - $totalExpense;

        if ($newBalance == 0) {
            return ['success' => false, 'message' => 'Saldo tidak boleh Rp0. Operasi dibatalkan.'];
        }

        $receiptPath = null;
        if ($receiptImage) {
            $receiptPath = $receiptImage->store('receipts', 'public');
        }

        $itemsCount = count($data['nominal']);
        for ($i = 0; $i < $itemsCount; $i++) {
            DB::table('pemasukan')->insert([
                'tanggal' => $data['tanggal'],
                'uraian' => $data['uraian'][$i] ?? '',
                'nominal' => $data['nominal'][$i],
                'foto_bukti' => $receiptPath,
                'id_jenis_penerimaan' => $data['id_jenis_penerimaan'][$i],
                'id_user' => session('user_id'),
                'is_confirmed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return ['success' => true];
    }

    public function update(array $data, int $id)
    {
        $baseItem = DB::table('pemasukan')->where('id_pemasukan', $id)->first();
        if (!$baseItem) {
            return ['success' => false, 'message' => 'Pemasukan tidak ditemukan.'];
        }

        $groupIds = DB::table('pemasukan')
            ->where('created_at', $baseItem->created_at)
            ->whereNull('deleted_at')
            ->pluck('id_pemasukan')
            ->toArray();

        $submittedIds = $data['id_pemasukan'] ?? [];
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

        return ['success' => true];
    }

    public function paginate(int $perPage = 5)
    {
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
            ->paginate($perPage);

        return $incomes;
    }

    public function findGroupById(int $id)
    {
        $baseItem = DB::table('pemasukan')->where('id_pemasukan', $id)->first();
        if (!$baseItem) return null;

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

        return ['group' => $incomes, 'base' => $incomes->first()];
    }

    public function getJenis()
    {
        return DB::table('jenis_penerimaan')->select('id_jenis_penerimaan as id', 'nama_jenis as nama')->get();
    }

    public function destroy(int $id)
    {
        $item = DB::table('pemasukan')->where('id_pemasukan', $id)->first();
        if ($item) {
            DB::table('pemasukan')
                ->where('created_at', $item->created_at)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);
        }

        return ['success' => true];
    }

    public function storeKategori(array $data)
    {
        $exists = DB::table('jenis_penerimaan')
            ->where('nama_jenis', $data['nama_jenis'])
            ->exists();

        if ($exists) {
            return ['success' => false, 'message' => 'Kategori sudah ada.'];
        }

        $id = DB::table('jenis_penerimaan')->insertGetId([
            'nama_jenis' => $data['nama_jenis'],
            'isAktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['success' => true, 'id' => $id, 'nama' => $data['nama_jenis']];
    }

    // ========== PEMISAHAN MANUAL vs OTOMATIS ==========

    public function storeManual(array $data, ?UploadedFile $receiptImage = null)
    {
        Log::debug('PemasukanService::storeManual received', ['data' => $data]);

        $totalNominal = 0;
        $itemsCount = count($data['nominal']);

        for ($i = 0; $i < $itemsCount; $i++) {
            $quantity = isset($data['kuantiti'][$i]) ? max(1, (int)$data['kuantiti'][$i]) : 1;
            $totalNominal += $data['nominal'][$i] * $quantity;
        }

        $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal') + $totalNominal;
        $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $newBalance = $totalIncome - $totalExpense;

        if ($newBalance == 0) {
            return ['success' => false, 'message' => 'Saldo tidak boleh Rp0. Operasi dibatalkan.'];
        }

        $receiptPath = null;
        if ($receiptImage) {
            $receiptPath = $receiptImage->store('receipts', 'public');
        }

        for ($i = 0; $i < $itemsCount; $i++) {
            $quantity = isset($data['kuantiti'][$i]) ? max(1, (int)$data['kuantiti'][$i]) : 1;
            $nominal = $data['nominal'][$i] * $quantity;
            $jenisId = $data['id_jenis_penerimaan'][$i] ?? $data['kategori_pemasukan'];

            DB::table('pemasukan')->insert([
                'tanggal' => $data['tanggal'],
                'uraian' => $data['uraian'][$i] ?? '',
                'nominal' => $nominal,
                'foto_bukti' => $receiptPath,
                'id_jenis_penerimaan' => $jenisId,
                'id_user' => session('user_id'),
                'is_confirmed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return ['success' => true];
    }
}
