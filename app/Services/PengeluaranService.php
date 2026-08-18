<?php

namespace App\Services;

<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
=======
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
>>>>>>> 0026227 (Baru)

class PengeluaranService
{
    public function store(array $data, ?UploadedFile $receiptImage = null)
    {
<<<<<<< HEAD
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
=======
        $totalNominal = $this->calculateTotal($data);
        $itemsCount = count($data['nominal']);

        if ($itemsCount < 1) {
            return ['success' => false, 'message' => 'Minimal satu item pengeluaran harus diisi.'];
        }

        $receiptPath = null;

        try {
            if ($receiptImage) {
                $receiptPath = $receiptImage->store('receipts', 'public');
            }

            DB::transaction(function () use ($data, $receiptPath, $totalNominal) {
                $currentBalance = $this->currentBalance();

                if ($totalNominal <= 0) {
                    throw new \RuntimeException('Total pengeluaran harus lebih dari Rp0.');
                }

                if ($totalNominal > $currentBalance) {
                    throw new \RuntimeException('Pengeluaran ditolak karena melebihi saldo tersedia. Saldo tersedia: Rp ' . number_format(max(0, $currentBalance), 0, ',', '.'));
                }

                $transactionGroupId = (string) Str::uuid();
                $createdAt = now();
                $rows = [];

                foreach ($data['nominal'] as $i => $nominal) {
                    $quantity = $this->quantityAt($data, $i);
                    $rows[] = [
                        'tanggal' => $data['tanggal'],
                        'uraian' => $data['uraian'][$i] ?? '',
                        'nominal' => $nominal * $quantity,
                        'quantity' => $quantity,
                        'foto_struk' => $receiptPath,
                        'id_jenis_pengeluaran' => $data['id_jenis_pengeluaran'][$i],
                        'id_user' => session('user_id'),
                        'is_confirmed' => 1,
                        'transaction_group_id' => $transactionGroupId,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }

                DB::table('pengeluaran')->insert($rows);

                // Final invariant: a successful transaction may never leave the account negative.
                $projectedBalance = $this->currentBalance();
                if ($projectedBalance < 0) {
                    throw new \RuntimeException('Transaksi dibatalkan karena saldo akan menjadi negatif.');
                }
            });

            return ['success' => true];
        } catch (\RuntimeException $e) {
            if ($receiptPath) Storage::disk('public')->delete($receiptPath);
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            if ($receiptPath) Storage::disk('public')->delete($receiptPath);
            Log::error('Gagal menyimpan pengeluaran.', ['message' => $e->getMessage(), 'user_id' => session('user_id')]);
            return ['success' => false, 'message' => 'Pengeluaran gagal disimpan. Silakan coba lagi.'];
        }
>>>>>>> 0026227 (Baru)
    }

    public function getJenis()
    {
<<<<<<< HEAD
        return DB::table('jenis_pengeluaran')->select('id_jenis_pengeluaran as id', 'nama_jenis as nama')->get();
=======
        return DB::table('jenis_pengeluaran')
            ->where('isAktif', true)
            ->select('id_jenis_pengeluaran as id', 'nama_jenis as nama')
            ->get();
>>>>>>> 0026227 (Baru)
    }

    public function paginate(int $perPage = 5)
    {
<<<<<<< HEAD
        $expenses = DB::table('pengeluaran')
=======
        return DB::table('pengeluaran')
>>>>>>> 0026227 (Baru)
            ->join('jenis_pengeluaran', 'pengeluaran.id_jenis_pengeluaran', '=', 'jenis_pengeluaran.id_jenis_pengeluaran')
            ->select(
                'pengeluaran.id_pengeluaran as id',
                'jenis_pengeluaran.nama_jenis as kategori',
                'pengeluaran.nominal as jumlah',
<<<<<<< HEAD
                'pengeluaran.tanggal as tanggal',
                'pengeluaran.uraian as uraian',
                'pengeluaran.created_at as created_at'
=======
                'pengeluaran.quantity as quantity',
                'pengeluaran.tanggal as tanggal',
                'pengeluaran.uraian as uraian',
                'pengeluaran.created_at as created_at',
                'pengeluaran.transaction_group_id as transaction_group_id'
>>>>>>> 0026227 (Baru)
            )
            ->whereNull('pengeluaran.deleted_at')
            ->orderBy('pengeluaran.tanggal', 'desc')
            ->paginate($perPage);
<<<<<<< HEAD

        return $expenses;
=======
>>>>>>> 0026227 (Baru)
    }

    public function findGroupById(int $id)
    {
<<<<<<< HEAD
        $baseItem = DB::table('pengeluaran')->where('id_pengeluaran', $id)->first();
        if (!$baseItem) return null;
=======
        $baseItem = DB::table('pengeluaran')
            ->where('id_pengeluaran', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$baseItem) {
            return null;
        }
>>>>>>> 0026227 (Baru)

        $expenses = DB::table('pengeluaran')
            ->join('jenis_pengeluaran', 'pengeluaran.id_jenis_pengeluaran', '=', 'jenis_pengeluaran.id_jenis_pengeluaran')
            ->select(
                'pengeluaran.id_pengeluaran as id',
                'jenis_pengeluaran.nama_jenis as kategori',
                'pengeluaran.nominal as jumlah',
<<<<<<< HEAD
=======
                'pengeluaran.quantity as quantity',
>>>>>>> 0026227 (Baru)
                'pengeluaran.tanggal as tanggal',
                'pengeluaran.uraian as uraian',
                'pengeluaran.foto_struk as foto_struk',
                'pengeluaran.id_jenis_pengeluaran as id_jenis_pengeluaran',
<<<<<<< HEAD
                'pengeluaran.is_confirmed as is_confirmed'
            )
            ->where('pengeluaran.created_at', $baseItem->created_at)
            ->whereNull('pengeluaran.deleted_at')
=======
                'pengeluaran.is_confirmed as is_confirmed',
                'pengeluaran.transaction_group_id as transaction_group_id'
            )
            ->where('pengeluaran.transaction_group_id', $baseItem->transaction_group_id)
            ->whereNull('pengeluaran.deleted_at')
            ->orderBy('pengeluaran.id_pengeluaran')
>>>>>>> 0026227 (Baru)
            ->get();

        return ['group' => $expenses, 'base' => $expenses->first()];
    }

    public function update(array $data, int $id)
    {
<<<<<<< HEAD
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
=======
        try {
            return DB::transaction(function () use ($data, $id) {
                $baseItem = DB::table('pengeluaran')
                    ->where('id_pengeluaran', $id)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->first();

                if (!$baseItem) {
                    return ['success' => false, 'message' => 'Pengeluaran tidak ditemukan.'];
                }

                $groupIds = DB::table('pengeluaran')
                    ->where('transaction_group_id', $baseItem->transaction_group_id)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->pluck('id_pengeluaran')
                    ->map(fn ($value) => (int) $value)
                    ->toArray();

                $submittedIds = array_values(array_filter(
                    array_map('intval', $data['id_pengeluaran'] ?? []),
                    fn ($value) => $value > 0
                ));

                if (count($submittedIds) !== count(array_unique($submittedIds))) {
                    return ['success' => false, 'message' => 'Data item pengeluaran duplikat.'];
                }

                if (!empty(array_diff($submittedIds, $groupIds))) {
                    return ['success' => false, 'message' => 'Data item pengeluaran tidak valid.'];
                }

                if (count($data['nominal']) < 1) {
                    return ['success' => false, 'message' => 'Minimal satu item pengeluaran harus dipertahankan.'];
                }

                $newTotalNominal = $this->calculateTotal($data);
                $otherExpenses = DB::table('pengeluaran')
                    ->where('is_confirmed', 1)
                    ->whereNull('deleted_at')
                    ->whereNotIn('id_pengeluaran', $groupIds)
                    ->sum('nominal');
                $totalIncome = DB::table('pemasukan')
                    ->where('is_confirmed', 1)
                    ->whereNull('deleted_at')
                    ->sum('nominal');
                $availableBalance = $totalIncome - $otherExpenses;

                // Crucially, validate the final quantity-adjusted total before soft-deleting anything.
                if ($newTotalNominal > $availableBalance) {
                    return ['success' => false, 'message' => 'Pengeluaran tidak boleh melebihi saldo. Saldo tersedia: Rp ' . number_format($availableBalance, 0, ',', '.')];
                }

                $deletedIds = array_diff($groupIds, $submittedIds);
                $now = now();

                foreach ($data['nominal'] as $i => $nominal) {
                    $itemId = $submittedIds[$i] ?? null;
                    $quantity = $this->quantityAt($data, $i);
                    $storedNominal = $nominal * $quantity;
                    $uraian = $data['uraian'][$i] ?? '';

                    if ($itemId !== null && in_array($itemId, $groupIds, true)) {
                        DB::table('pengeluaran')
                            ->where('id_pengeluaran', $itemId)
                            ->update([
                                'tanggal' => $data['tanggal'],
                                'uraian' => $uraian,
                                'nominal' => $storedNominal,
                                'quantity' => $quantity,
                                'id_jenis_pengeluaran' => $data['id_jenis_pengeluaran'],
                                'updated_at' => $now,
                            ]);
                    } else {
                        DB::table('pengeluaran')->insert([
                            'tanggal' => $data['tanggal'],
                            'uraian' => $uraian,
                            'nominal' => $storedNominal,
                            'quantity' => $quantity,
                            'foto_struk' => $baseItem->foto_struk,
                            'id_jenis_pengeluaran' => $data['id_jenis_pengeluaran'],
                            'id_user' => session('user_id'),
                            'is_confirmed' => 1,
                            'transaction_group_id' => $baseItem->transaction_group_id,
                            'created_at' => $baseItem->created_at,
                            'updated_at' => $now,
                        ]);
                    }
                }

                if (!empty($deletedIds)) {
                    DB::table('pengeluaran')
                        ->whereIn('id_pengeluaran', $deletedIds)
                        ->update(['deleted_at' => $now, 'updated_at' => $now]);
                }

                if ($this->currentBalance() < 0) {
                    throw new \RuntimeException('Perubahan dibatalkan karena saldo akan menjadi negatif.');
                }

                return ['success' => true];
            });
        } catch (Throwable $e) {
            Log::error('Gagal memperbarui pengeluaran.', [
                'id' => $id,
                'message' => $e->getMessage(),
                'user_id' => session('user_id'),
            ]);

            return ['success' => false, 'message' => 'Pengeluaran gagal diperbarui. Tidak ada perubahan yang disimpan.'];
        }
>>>>>>> 0026227 (Baru)
    }

    public function destroy(int $id)
    {
<<<<<<< HEAD
        $item = DB::table('pengeluaran')->where('id_pengeluaran', $id)->first();
        if ($item) {
            DB::table('pengeluaran')
                ->where('created_at', $item->created_at)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);
        }

        return ['success' => true];
    }

    // ========== PEMISAHAN MANUAL vs OTOMATIS ==========

    public function storeManual(array $data, ?UploadedFile $receiptImage = null)
    {
        Log::debug('PengeluaranService::storeManual received', ['data' => $data]);

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
            $jenisId = $data['id_jenis_pengeluaran'][$i] ?? $data['kategori_pengeluaran'];

            DB::table('pengeluaran')->insert([
                'tanggal' => $data['tanggal'],
                'uraian' => $data['uraian'][$i] ?? '',
                'nominal' => $nominal,
                'foto_struk' => $receiptPath,
                'id_jenis_pengeluaran' => $jenisId,
                'id_user' => session('user_id'),
                'is_confirmed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return ['success' => true];
    }
}

=======
        try {
            return DB::transaction(function () use ($id) {
                $item = DB::table('pengeluaran')
                    ->where('id_pengeluaran', $id)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->first();

                if (!$item) {
                    return ['success' => false, 'message' => 'Pengeluaran tidak ditemukan.'];
                }

                DB::table('pengeluaran')
                    ->where('transaction_group_id', $item->transaction_group_id)
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => now(), 'updated_at' => now()]);

                return ['success' => true];
            });
        } catch (Throwable $e) {
            Log::error('Gagal menghapus pengeluaran.', ['id' => $id, 'message' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Pengeluaran gagal dihapus.'];
        }
    }

    public function storeManual(array $data, ?UploadedFile $receiptImage = null)
    {
        $totalNominal = $this->calculateTotal($data);
        $itemsCount = count($data['nominal']);

        if ($itemsCount < 1) {
            return ['success' => false, 'message' => 'Minimal satu item pengeluaran harus diisi.'];
        }

        $receiptPath = null;

        try {
            if ($receiptImage) {
                $receiptPath = $receiptImage->store('receipts', 'public');
            }

            DB::transaction(function () use ($data, $receiptPath, $itemsCount, $totalNominal) {
                $currentBalance = $this->currentBalance();

                if ($totalNominal <= 0) {
                    throw new \RuntimeException('Total pengeluaran harus lebih dari Rp0.');
                }

                if ($totalNominal > $currentBalance) {
                    throw new \RuntimeException('Pengeluaran ditolak karena melebihi saldo tersedia. Saldo tersedia: Rp ' . number_format(max(0, $currentBalance), 0, ',', '.'));
                }

                $transactionGroupId = (string) Str::uuid();
                $createdAt = now();
                $rows = [];

                for ($i = 0; $i < $itemsCount; $i++) {
                    $quantity = $this->quantityAt($data, $i);
                    $nominal = $data['nominal'][$i] * $quantity;
                    $jenisId = $data['id_jenis_pengeluaran'][$i] ?? $data['kategori_pengeluaran'];

                    $rows[] = [
                        'tanggal' => $data['tanggal'],
                        'uraian' => $data['uraian'][$i] ?? '',
                        'nominal' => $nominal,
                        'quantity' => $quantity,
                        'foto_struk' => $receiptPath,
                        'id_jenis_pengeluaran' => $jenisId,
                        'id_user' => session('user_id'),
                        'is_confirmed' => 1,
                        'transaction_group_id' => $transactionGroupId,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }

                DB::table('pengeluaran')->insert($rows);

                // Final invariant for the manual form as well.
                $projectedBalance = $this->currentBalance();
                if ($projectedBalance < 0) {
                    throw new \RuntimeException('Transaksi dibatalkan karena saldo akan menjadi negatif.');
                }
            });

            return ['success' => true];
        } catch (\RuntimeException $e) {
            if ($receiptPath) Storage::disk('public')->delete($receiptPath);
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            if ($receiptPath) Storage::disk('public')->delete($receiptPath);
            Log::error('Gagal menyimpan pengeluaran manual.', ['message' => $e->getMessage(), 'user_id' => session('user_id')]);
            return ['success' => false, 'message' => 'Pengeluaran gagal disimpan. Silakan coba lagi.'];
        }
    }

    private function quantityAt(array $data, int $index): int
    {
        return isset($data['kuantiti'][$index]) ? max(1, (int) $data['kuantiti'][$index]) : 1;
    }

    private function calculateTotal(array $data): float
    {
        $total = 0;
        foreach ($data['nominal'] as $i => $nominal) {
            $total += (float) $nominal * $this->quantityAt($data, $i);
        }
        return $total;
    }

    private function currentBalance(): float
    {
        $totalIncome = DB::table('pemasukan')
            ->where('is_confirmed', 1)
            ->whereNull('deleted_at')
            ->sum('nominal');
        $totalExpense = DB::table('pengeluaran')
            ->where('is_confirmed', 1)
            ->whereNull('deleted_at')
            ->sum('nominal');

        return (float) $totalIncome - (float) $totalExpense;
    }
}
>>>>>>> 0026227 (Baru)
