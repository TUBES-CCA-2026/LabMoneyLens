<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class RecycleService
{
    public function list()
    {
        $deletedIncome = DB::table('pemasukan')
            ->join('jenis_penerimaan', 'pemasukan.id_jenis_penerimaan', '=', 'jenis_penerimaan.id_jenis_penerimaan')
            ->select(
                'pemasukan.id_pemasukan as id',
                'pemasukan.transaction_group_id',
                'jenis_penerimaan.nama_jenis as kategori',
                'pemasukan.nominal as jumlah',
                'pemasukan.tanggal as tanggal',
                'pemasukan.deleted_at as deleted_at',
                DB::raw("'Pemasukan' as tipe")
            )
            ->whereNotNull('pemasukan.deleted_at')
            ->get();

        $deletedExpense = DB::table('pengeluaran')
            ->join('jenis_pengeluaran', 'pengeluaran.id_jenis_pengeluaran', '=', 'jenis_pengeluaran.id_jenis_pengeluaran')
            ->select(
                'pengeluaran.id_pengeluaran as id',
                'pengeluaran.transaction_group_id',
                'jenis_pengeluaran.nama_jenis as kategori',
                'pengeluaran.nominal as jumlah',
                'pengeluaran.tanggal as tanggal',
                'pengeluaran.deleted_at as deleted_at',
                DB::raw("'Pengeluaran' as tipe")
            )
            ->whereNotNull('pengeluaran.deleted_at')
            ->get();

        $recordsCollection = $deletedIncome->concat($deletedExpense)
            ->groupBy(fn ($item) => $item->tipe . '|' . $item->transaction_group_id)
            ->map(function ($group) {
                $first = $group->first();
                $categories = $group->pluck('kategori')->unique()->values();

                return (object) [
                    'id' => $first->id,
                    'kategori' => $categories->count() > 1 ? 'Gabungan Kategori' : $categories->first(),
                    'jumlah' => $group->sum('jumlah'),
                    'tanggal' => $first->tanggal,
                    'deleted_at' => $group->max('deleted_at'),
                    'tipe' => $first->tipe,
                    'item_count' => $group->count(),
                    'transaction_group_id' => $first->transaction_group_id,
                ];
            })
            ->sortByDesc('deleted_at')
            ->values();

        $totalItems = $recordsCollection->count();
        $totalValue = $recordsCollection->sum('jumlah');

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $records = new LengthAwarePaginator(
            $recordsCollection->forPage($currentPage, $perPage),
            $totalItems,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return compact('records', 'totalItems', 'totalValue');
    }

    public function restore(string $type, $id)
    {
        try {
            return DB::transaction(function () use ($type, $id) {
                if ($type === 'pemasukan') {
                    $record = DB::table('pemasukan')->where('id_pemasukan', $id)->whereNotNull('deleted_at')->lockForUpdate()->first();
                    if (!$record) return ['success' => false, 'message' => 'Transaksi pemasukan tidak ditemukan di Recycle Bin.'];

                    DB::table('pemasukan')
                        ->where('transaction_group_id', $record->transaction_group_id)
                        ->whereNotNull('deleted_at')
                        ->update(['deleted_at' => null, 'updated_at' => now()]);

                    return ['success' => true];
                }

                if ($type === 'pengeluaran') {
                    $record = DB::table('pengeluaran')->where('id_pengeluaran', $id)->whereNotNull('deleted_at')->lockForUpdate()->first();
                    if (!$record) return ['success' => false, 'message' => 'Transaksi pengeluaran tidak ditemukan di Recycle Bin.'];

                    $groupId = $record->transaction_group_id;
                    $restoreExpense = DB::table('pengeluaran')
                        ->where('transaction_group_id', $groupId)
                        ->whereNotNull('deleted_at')
                        ->sum('nominal');
                    $totalIncome = DB::table('pemasukan')
                        ->where('is_confirmed', 1)
                        ->whereNull('deleted_at')
                        ->sum('nominal');
                    $activeExpense = DB::table('pengeluaran')
                        ->where('is_confirmed', 1)
                        ->whereNull('deleted_at')
                        ->sum('nominal');
                    $projectedBalance = (float) $totalIncome - ((float) $activeExpense + (float) $restoreExpense);

                    if ($projectedBalance < 0) {
                        return [
                            'success' => false,
                            'message' => 'Pengeluaran tidak dapat dipulihkan karena saldo akan menjadi negatif. Saldo setelah pemulihan: Rp ' . number_format($projectedBalance, 0, ',', '.'),
                        ];
                    }

                    DB::table('pengeluaran')
                        ->where('transaction_group_id', $groupId)
                        ->whereNotNull('deleted_at')
                        ->update(['deleted_at' => null, 'updated_at' => now()]);

                    return ['success' => true];
                }

                return ['success' => false, 'message' => 'Jenis transaksi tidak valid.'];
            });
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Transaksi gagal dipulihkan.'];
        }
    }

    public function forceDelete(string $type, $id)
    {
        try {
            return DB::transaction(function () use ($type, $id) {
                if ($type === 'pemasukan') {
                    $record = DB::table('pemasukan')->where('id_pemasukan', $id)->whereNotNull('deleted_at')->lockForUpdate()->first();
                    if (!$record) return ['success' => false, 'message' => 'Transaksi pemasukan tidak ditemukan di Recycle Bin.'];

                    $groupId = $record->transaction_group_id;
                    $paths = DB::table('pemasukan')->where('transaction_group_id', $groupId)->pluck('foto_bukti')->filter()->unique()->values()->all();
                    DB::table('pemasukan')->where('transaction_group_id', $groupId)->whereNotNull('deleted_at')->delete();

                    return ['success' => true, 'paths' => $paths];
                }

                if ($type === 'pengeluaran') {
                    $record = DB::table('pengeluaran')->where('id_pengeluaran', $id)->whereNotNull('deleted_at')->lockForUpdate()->first();
                    if (!$record) return ['success' => false, 'message' => 'Transaksi pengeluaran tidak ditemukan di Recycle Bin.'];

                    $groupId = $record->transaction_group_id;
                    $paths = DB::table('pengeluaran')->where('transaction_group_id', $groupId)->pluck('foto_struk')->filter()->unique()->values()->all();
                    DB::table('pengeluaran')->where('transaction_group_id', $groupId)->whereNotNull('deleted_at')->delete();

                    return ['success' => true, 'paths' => $paths];
                }

                return ['success' => false, 'message' => 'Jenis transaksi tidak valid.'];
            });
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Transaksi gagal dihapus permanen.'];
        }
    }

    public function restoreAll()
    {
        return DB::transaction(function () {
            $deletedIncome = DB::table('pemasukan')->whereNotNull('deleted_at')->sum('nominal');
            $deletedExpense = DB::table('pengeluaran')->whereNotNull('deleted_at')->sum('nominal');
            $activeIncome = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
            $activeExpense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');

            $projectedBalance = ((float) $activeIncome + (float) $deletedIncome)
                - ((float) $activeExpense + (float) $deletedExpense);

            if ($projectedBalance < 0) {
                return [
                    'success' => false,
                    'message' => 'Semua transaksi tidak dapat dipulihkan karena saldo akan menjadi negatif. Saldo setelah pemulihan: Rp ' . number_format($projectedBalance, 0, ',', '.'),
                ];
            }

            DB::table('pemasukan')->whereNotNull('deleted_at')->update(['deleted_at' => null, 'updated_at' => now()]);
            DB::table('pengeluaran')->whereNotNull('deleted_at')->update(['deleted_at' => null, 'updated_at' => now()]);
            return ['success' => true];
        });
    }

    public function emptyTrash()
    {
        return DB::transaction(function () {
            $incomePaths = DB::table('pemasukan')->whereNotNull('deleted_at')->pluck('foto_bukti')->filter()->unique()->values()->all();
            $expensePaths = DB::table('pengeluaran')->whereNotNull('deleted_at')->pluck('foto_struk')->filter()->unique()->values()->all();

            DB::table('pemasukan')->whereNotNull('deleted_at')->delete();
            DB::table('pengeluaran')->whereNotNull('deleted_at')->delete();

            return ['success' => true, 'paths' => array_values(array_unique(array_merge($incomePaths, $expensePaths)))];
        });
    }

    public function deleteReturnedFiles(array $paths): void
    {
        foreach (array_filter(array_unique($paths)) as $path) {
            $stillReferenced = DB::table('pemasukan')->where('foto_bukti', $path)->exists()
                || DB::table('pengeluaran')->where('foto_struk', $path)->exists();

            if (!$stillReferenced) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
