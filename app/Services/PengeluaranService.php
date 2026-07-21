<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class PengeluaranService
{
    public function store(array $data, ?UploadedFile $receiptImage = null)
    {
        Log::debug('PengeluaranService::store received nominal', ['nominal' => $data['nominal'] ?? null]);
        if (isset($data['nominal']) && is_array($data['nominal'])) {
            foreach ($data['nominal'] as $i => $val) {
                Log::debug('PengeluaranService::store nominal item', ['index' => $i, 'raw' => $val]);
            }
        }
        $totalNominal = 0;
        $itemsCount = count($data['nominal']);

        for ($i = 0; $i < $itemsCount; $i++) {
            $quantity = isset($data['kuantiti'][$i]) ? max(1, (int)$data['kuantiti'][$i]) : 1;
            $totalNominal += $data['nominal'][$i] * $quantity;
        }

        $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $currentBalance = $totalIncome - $totalExpense;

        if ($totalNominal > $currentBalance) {
            return ['success' => false, 'message' => 'Total pengeluaran tidak boleh melebihi saldo. Saldo Anda: Rp ' . number_format($currentBalance, 0, ',', '.')];
        }

        $receiptPath = null;
        if ($receiptImage) {
            $receiptPath = $receiptImage->store('receipts', 'public');
        }

        for ($i = 0; $i < $itemsCount; $i++) {
            $quantity = isset($data['kuantiti'][$i]) ? max(1, (int)$data['kuantiti'][$i]) : 1;
            $nominal = $data['nominal'][$i] * $quantity;

            DB::table('pengeluaran')->insert([
                'tanggal' => $data['tanggal'],
                'uraian' => $data['uraian'][$i] ?? '',
                'nominal' => $nominal,
                'foto_struk' => $receiptPath,
                'id_jenis_pengeluaran' => $data['id_jenis_pengeluaran'][$i],
                'id_user' => session('user_id'),
                'is_confirmed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return ['success' => true];
    }

    public function getJenis()
    {
        return DB::table('jenis_pengeluaran')->select('id_jenis_pengeluaran as id', 'nama_jenis as nama')->get();
    }

    public function paginate(int $perPage = 5)
    {
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
            ->paginate($perPage);

        return $expenses;
    }

    public function findGroupById(int $id)
    {
        $baseItem = DB::table('pengeluaran')->where('id_pengeluaran', $id)->first();
        if (!$baseItem) return null;

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

        return ['group' => $expenses, 'base' => $expenses->first()];
    }

    public function update(array $data, int $id)
    {
        $baseItem = DB::table('pengeluaran')->where('id_pengeluaran', $id)->first();
        if (!$baseItem) return ['success' => false, 'message' => 'Pengeluaran tidak ditemukan.'];

        $groupIds = DB::table('pengeluaran')
            ->where('created_at', $baseItem->created_at)
            ->whereNull('deleted_at')
            ->pluck('id_pengeluaran')->toArray();

        $submittedIds = $data['id_pengeluaran'] ?? [];
        $deletedIds = array_diff($groupIds, $submittedIds);
        if (!empty($deletedIds)) {
            DB::table('pengeluaran')->whereIn('id_pengeluaran', $deletedIds)->update(['deleted_at' => now()]);
        }

        $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $totalExpenseOther = DB::table('pengeluaran')
            ->where('is_confirmed', 1)
            ->whereNull('deleted_at')
            ->whereNotIn('id_pengeluaran', $groupIds)
            ->sum('nominal');

        $currentBalance = $totalIncome - $totalExpenseOther;
        $newTotalNominal = array_sum($data['nominal']);

        if ($newTotalNominal > $currentBalance) {
            return ['success' => false, 'message' => 'Pengeluaran tidak boleh melebihi saldo. Saldo tersedia: Rp ' . number_format($currentBalance, 0, ',', '.')];
        }

        for ($i = 0; $i < count($data['nominal']); $i++) {
            $itemId   = $submittedIds[$i] ?? null;
            $kuantiti = isset($data['kuantiti'][$i]) ? (int)$data['kuantiti'][$i] : 1;
            $kuantiti = max(1, $kuantiti);
            $nominal  = $data['nominal'][$i] * $kuantiti;
            $uraian   = $data['uraian'][$i] ?? '';

            if ($itemId && in_array($itemId, $groupIds)) {
                DB::table('pengeluaran')->where('id_pengeluaran', $itemId)->update([
                    'tanggal'              => $data['tanggal'],
                    'uraian'               => $uraian,
                    'nominal'              => $nominal,
                    'id_jenis_pengeluaran' => $data['id_jenis_pengeluaran'],
                    'updated_at'           => now(),
                ]);
            } else {
                DB::table('pengeluaran')->insert([
                    'tanggal'              => $data['tanggal'],
                    'uraian'               => $uraian,
                    'nominal'              => $nominal,
                    'foto_struk'           => $baseItem->foto_struk,
                    'id_jenis_pengeluaran' => $data['id_jenis_pengeluaran'],
                    'id_user'              => session('user_id'),
                    'is_confirmed'         => 1,
                    'created_at'           => $baseItem->created_at,
                    'updated_at'           => now(),
                ]);
            }
        }

        return ['success' => true];
    }

    public function destroy(int $id)
    {
        $item = DB::table('pengeluaran')->where('id_pengeluaran', $id)->first();
        if ($item) {
            DB::table('pengeluaran')
                ->where('created_at', $item->created_at)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);
        }

        return ['success' => true];
    }
}
