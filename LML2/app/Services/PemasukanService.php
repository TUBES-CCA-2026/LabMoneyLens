<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class PemasukanService
{
    public function store(array $data, ?UploadedFile $receiptImage = null)
    {
        $receiptPath = null;

        try {
            $totalNominal = $this->calculateTotal($data);
            $itemsCount = count($data['nominal']);

            if ($itemsCount < 1) {
                return ['success' => false, 'message' => 'Minimal satu item pemasukan harus diisi.'];
            }

            if ($receiptImage) {
                $receiptPath = $receiptImage->store('receipts', 'public');
            }

            DB::transaction(function () use ($data, $receiptPath, $totalNominal) {
                $totalIncome = DB::table('pemasukan')
                    ->where('is_confirmed', 1)
                    ->whereNull('deleted_at')
                    ->sum('nominal') + $totalNominal;
                $totalExpense = DB::table('pengeluaran')
                    ->where('is_confirmed', 1)
                    ->whereNull('deleted_at')
                    ->sum('nominal');
                $newBalance = $totalIncome - $totalExpense;

                // A zero balance is valid. Income may never create a negative balance,
                // but adding income cannot make an already-valid balance negative.
                if ($newBalance < 0) {
                    throw new \RuntimeException('Pemasukan ditolak karena saldo akan menjadi negatif.');
                }

                $transactionGroupId = (string) Str::uuid();
                $createdAt = now();
                $rows = [];

                foreach ($data['nominal'] as $i => $nominal) {
                    $quantity = isset($data['kuantiti'][$i]) ? max(1, (int) $data['kuantiti'][$i]) : 1;
                    $storedNominal = (float) $nominal * $quantity;
                    $rows[] = [
                        'tanggal' => $data['tanggal'],
                        'uraian' => $data['uraian'][$i] ?? '',
                        'nominal' => $storedNominal,
                        'quantity' => $quantity,
                        'foto_bukti' => $receiptPath,
                        'id_jenis_penerimaan' => $data['id_jenis_penerimaan'][$i],
                        'id_user' => session('user_id'),
                        'is_confirmed' => 1,
                        'transaction_group_id' => $transactionGroupId,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }

                DB::table('pemasukan')->insert($rows);
            });

            return ['success' => true];
        } catch (\RuntimeException $e) {
            if ($receiptPath) {
                Storage::disk('public')->delete($receiptPath);
            }

            return ['success' => false, 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            if ($receiptPath) {
                Storage::disk('public')->delete($receiptPath);
            }

            Log::error('Gagal menyimpan pemasukan.', [
                'message' => $e->getMessage(),
                'user_id' => session('user_id'),
            ]);

            return ['success' => false, 'message' => 'Pemasukan gagal disimpan. Silakan coba lagi.'];
        }
    }

    public function update(array $data, int $id)
    {
        try {
            return DB::transaction(function () use ($data, $id) {
                $baseItem = DB::table('pemasukan')
                    ->where('id_pemasukan', $id)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->first();

                if (!$baseItem) {
                    return ['success' => false, 'message' => 'Pemasukan tidak ditemukan.'];
                }

                $groupIds = DB::table('pemasukan')
                    ->where('transaction_group_id', $baseItem->transaction_group_id)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->pluck('id_pemasukan')
                    ->map(fn ($value) => (int) $value)
                    ->toArray();

                $submittedIds = array_values(array_filter(
                    array_map('intval', $data['id_pemasukan'] ?? []),
                    fn ($value) => $value > 0
                ));

                if (count($submittedIds) !== count(array_unique($submittedIds))) {
                    return ['success' => false, 'message' => 'Data item pemasukan duplikat.'];
                }

                $invalidSubmittedIds = array_diff($submittedIds, $groupIds);
                if (!empty($invalidSubmittedIds)) {
                    return ['success' => false, 'message' => 'Data item pemasukan tidak valid.'];
                }

                if (count($data['nominal']) < 1) {
                    return ['success' => false, 'message' => 'Minimal satu item pemasukan harus dipertahankan.'];
                }

                $deletedIds = array_diff($groupIds, $submittedIds);

                // Validate the final balance BEFORE mutating any row.
                // The edited group is excluded from the current income total, then
                // the newly submitted total is added back. This prevents an income
                // edit from making the account balance negative.
                $newTotalIncome = DB::table('pemasukan')
                    ->where('is_confirmed', 1)
                    ->whereNull('deleted_at')
                    ->whereNotIn('id_pemasukan', $groupIds)
                    ->sum('nominal') + $this->calculateTotal($data);
                $totalExpense = DB::table('pengeluaran')
                    ->where('is_confirmed', 1)
                    ->whereNull('deleted_at')
                    ->sum('nominal');
                $projectedBalance = (float) $newTotalIncome - (float) $totalExpense;

                if ($projectedBalance < 0) {
                    return [
                        'success' => false,
                        'message' => 'Perubahan pemasukan ditolak karena saldo akan menjadi negatif. Saldo setelah perubahan: Rp ' . number_format($projectedBalance, 0, ',', '.'),
                    ];
                }

                $now = now();

                foreach ($data['nominal'] as $i => $nominal) {
                    $itemId = $submittedIds[$i] ?? null;
                    $quantity = isset($data['kuantiti'][$i]) ? max(1, (int) $data['kuantiti'][$i]) : 1;
                    $storedNominal = (float) $nominal * $quantity;
                    $uraian = $data['uraian'][$i] ?? '';

                    if ($itemId !== null && in_array($itemId, $groupIds, true)) {
                        DB::table('pemasukan')
                            ->where('id_pemasukan', $itemId)
                            ->update([
                                'tanggal' => $data['tanggal'],
                                'uraian' => $uraian,
                                'nominal' => $storedNominal,
                                'quantity' => $quantity,
                                'id_jenis_penerimaan' => $data['id_jenis_penerimaan'],
                                'updated_at' => $now,
                            ]);
                    } else {
                        DB::table('pemasukan')->insert([
                            'tanggal' => $data['tanggal'],
                            'uraian' => $uraian,
                            'nominal' => $storedNominal,
                            'quantity' => $quantity,
                            'foto_bukti' => $baseItem->foto_bukti,
                            'id_jenis_penerimaan' => $data['id_jenis_penerimaan'],
                            'id_user' => session('user_id'),
                            'is_confirmed' => 1,
                            'transaction_group_id' => $baseItem->transaction_group_id,
                            'created_at' => $baseItem->created_at,
                            'updated_at' => $now,
                        ]);
                    }
                }

                if (!empty($deletedIds)) {
                    DB::table('pemasukan')
                        ->whereIn('id_pemasukan', $deletedIds)
                        ->update(['deleted_at' => $now, 'updated_at' => $now]);
                }

                return ['success' => true];
            });
        } catch (\RuntimeException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            Log::error('Gagal memperbarui pemasukan.', [
                'id' => $id,
                'message' => $e->getMessage(),
                'user_id' => session('user_id'),
            ]);

            return ['success' => false, 'message' => 'Pemasukan gagal diperbarui. Tidak ada perubahan yang disimpan.'];
        }
    }

    public function paginate(int $perPage = 5)
    {
        // Riwayat tetap menampilkan SETIAP ITEM sebagai baris terpisah.
        // Namun item dari struk/transaksi yang sama harus selalu berdekatan.
        // Karena itu urutan menggunakan transaction_group_id terlebih dahulu,
        // lalu tanggal dan id item. Data tidak digabung dan nominal setiap item
        // tetap ditampilkan apa adanya.
        return DB::table('pemasukan')
            ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
            ->select(
                'pemasukan.id_pemasukan as id',
                'jenis_penerimaan.nama_jenis as kategori',
                'pemasukan.nominal as jumlah',
                'pemasukan.quantity as quantity',
                'pemasukan.tanggal as tanggal',
                'pemasukan.uraian as uraian',
                'pemasukan.created_at as created_at',
                'pemasukan.transaction_group_id as transaction_group_id'
            )
            ->whereNull('pemasukan.deleted_at')
            ->orderByDesc('pemasukan.tanggal')
            ->orderBy('pemasukan.transaction_group_id')
            ->orderBy('pemasukan.id_pemasukan')
            ->paginate($perPage);
    }

    public function findGroupById(int $id)
    {
        $baseItem = DB::table('pemasukan')
            ->where('id_pemasukan', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$baseItem) {
            return null;
        }

        $incomes = DB::table('pemasukan')
            ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
            ->select(
                'pemasukan.id_pemasukan as id',
                'jenis_penerimaan.nama_jenis as kategori',
                'pemasukan.nominal as jumlah',
                'pemasukan.quantity as quantity',
                'pemasukan.tanggal as tanggal',
                'pemasukan.uraian as uraian',
                'pemasukan.foto_bukti as foto_struk',
                'pemasukan.id_jenis_penerimaan as id_jenis_penerimaan',
                'pemasukan.transaction_group_id as transaction_group_id'
            )
            ->where('pemasukan.transaction_group_id', $baseItem->transaction_group_id)
            ->whereNull('pemasukan.deleted_at')
            ->orderBy('pemasukan.id_pemasukan')
            ->get();

        return ['group' => $incomes, 'base' => $incomes->first()];
    }

    public function getJenis()
    {
        return DB::table('jenis_penerimaan')
            ->where('isAktif', true)
            ->select('id_jenis_penerimaan as id', 'nama_jenis as nama')
            ->get();
    }

    private function calculateTotal(array $data): float
    {
        $total = 0;
        foreach ($data['nominal'] as $i => $nominal) {
            $quantity = isset($data['kuantiti'][$i]) ? max(1, (int) $data['kuantiti'][$i]) : 1;
            $total += (float) $nominal * $quantity;
        }
        return $total;
    }

    public function destroy(int $id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $item = DB::table('pemasukan')
                    ->where('id_pemasukan', $id)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->first();

                if (!$item) {
                    return ['success' => false, 'message' => 'Pemasukan tidak ditemukan.'];
                }

                $groupId = $item->transaction_group_id;
                $otherIncome = DB::table('pemasukan')
                    ->where('is_confirmed', 1)
                    ->whereNull('deleted_at')
                    ->where('transaction_group_id', '!=', $groupId)
                    ->sum('nominal');
                $totalExpense = DB::table('pengeluaran')
                    ->where('is_confirmed', 1)
                    ->whereNull('deleted_at')
                    ->sum('nominal');
                $projectedBalance = (float) $otherIncome - (float) $totalExpense;

                if ($projectedBalance < 0) {
                    return [
                        'success' => false,
                        'message' => 'Pemasukan tidak dapat dihapus karena saldo akan menjadi negatif. Saldo setelah penghapusan: Rp ' . number_format($projectedBalance, 0, ',', '.'),
                    ];
                }

                DB::table('pemasukan')
                    ->where('transaction_group_id', $groupId)
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => now(), 'updated_at' => now()]);

                return ['success' => true];
            });
        } catch (Throwable $e) {
            Log::error('Gagal menghapus pemasukan.', ['id' => $id, 'message' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Pemasukan gagal dihapus.'];
        }
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

    public function storeManual(array $data, ?UploadedFile $receiptImage = null)
    {
        $totalNominal = 0;
        $itemsCount = count($data['nominal']);

        if ($itemsCount < 1) {
            return ['success' => false, 'message' => 'Minimal satu item pemasukan harus diisi.'];
        }

        for ($i = 0; $i < $itemsCount; $i++) {
            $quantity = isset($data['kuantiti'][$i]) ? max(1, (int) $data['kuantiti'][$i]) : 1;
            $totalNominal += $data['nominal'][$i] * $quantity;
        }

        $receiptPath = null;

        try {
            if ($receiptImage) {
                $receiptPath = $receiptImage->store('receipts', 'public');
            }

            DB::transaction(function () use ($data, $receiptPath, $itemsCount, $totalNominal) {
                $totalIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal') + $totalNominal;
                $totalExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
                $newBalance = $totalIncome - $totalExpense;

                if ($newBalance < 0) {
                    throw new \RuntimeException('Pemasukan ditolak karena saldo akan menjadi negatif.');
                }

                $transactionGroupId = (string) Str::uuid();
                $createdAt = now();
                $rows = [];

                for ($i = 0; $i < $itemsCount; $i++) {
                    $quantity = isset($data['kuantiti'][$i]) ? max(1, (int) $data['kuantiti'][$i]) : 1;
                    $nominal = $data['nominal'][$i] * $quantity;
                    $jenisId = $data['id_jenis_penerimaan'][$i] ?? $data['kategori_pemasukan'];

                    $rows[] = [
                        'tanggal' => $data['tanggal'],
                        'uraian' => $data['uraian'][$i] ?? '',
                        'nominal' => $nominal,
                        'quantity' => $quantity,
                        'foto_bukti' => $receiptPath,
                        'id_jenis_penerimaan' => $jenisId,
                        'id_user' => session('user_id'),
                        'is_confirmed' => 1,
                        'transaction_group_id' => $transactionGroupId,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }

                DB::table('pemasukan')->insert($rows);
            });

            return ['success' => true];
        } catch (\RuntimeException $e) {
            if ($receiptPath) Storage::disk('public')->delete($receiptPath);
            return ['success' => false, 'message' => $e->getMessage()];
        } catch (Throwable $e) {
            if ($receiptPath) Storage::disk('public')->delete($receiptPath);
            Log::error('Gagal menyimpan pemasukan manual.', ['message' => $e->getMessage(), 'user_id' => session('user_id')]);
            return ['success' => false, 'message' => 'Pemasukan gagal disimpan. Silakan coba lagi.'];
        }
    }
}
