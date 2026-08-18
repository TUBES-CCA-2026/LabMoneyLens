<?php

namespace App\Services;

use App\Models\Pengeluaran;
use App\Models\Pemasukan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StrukService
{
    public function list()
    {
        $pemasukan = Pemasukan::whereNotNull('foto_bukti')
            ->where('foto_bukti', '!=', '')
            ->whereNull('deleted_at')
            ->with('jenisPenerimaan')
            ->get()
            ->groupBy('transaction_group_id')
            ->map(function ($group) {
                $first = $group->first();
                $count = $group->count();
                $uraian = $count > 1 ? $count . ' Item (Termasuk: ' . $first->uraian . ')' : $first->uraian;

                return (object) [
                    'id' => $first->id_pemasukan,
                    'type' => 'Pemasukan',
                    'kategori' => $group->pluck('jenisPenerimaan.nama_jenis')->filter()->unique()->count() > 1
                        ? 'Gabungan Kategori'
                        : ($first->jenisPenerimaan?->nama_jenis ?? 'Lainnya'),
                    'tanggal' => $first->tanggal,
                    'uraian' => $uraian,
                    'nominal' => $group->sum('nominal'),
                    'foto' => $first->foto_bukti,
                ];
            })
            ->values();

        $pengeluaran = Pengeluaran::whereNotNull('foto_struk')
            ->where('foto_struk', '!=', '')
            ->whereNull('deleted_at')
            ->with('jenisPengeluaran')
            ->get()
            ->groupBy('transaction_group_id')
            ->map(function ($group) {
                $first = $group->first();
                $count = $group->count();
                $uraian = $count > 1 ? $count . ' Item (Termasuk: ' . $first->uraian . ')' : $first->uraian;

                return (object) [
                    'id' => $first->id_pengeluaran,
                    'type' => 'Pengeluaran',
                    'kategori' => $group->pluck('jenisPengeluaran.nama_jenis')->filter()->unique()->count() > 1
                        ? 'Gabungan Kategori'
                        : ($first->jenisPengeluaran?->nama_jenis ?? 'Lainnya'),
                    'tanggal' => $first->tanggal,
                    'uraian' => $uraian,
                    'nominal' => $group->sum('nominal'),
                    'foto' => $first->foto_struk,
                ];
            })
            ->values();

        $strukList = $pemasukan->concat($pengeluaran)
            ->sortByDesc('tanggal')
            ->values();

        $kategoriPengeluaran = DB::table('jenis_pengeluaran')->where('isAktif', true)->pluck('nama_jenis')->sort()->values();
        $kategoriPemasukan = DB::table('jenis_penerimaan')->where('isAktif', true)->pluck('nama_jenis')->sort()->values();

        return compact('strukList', 'kategoriPemasukan', 'kategoriPengeluaran');
    }

    public function resolvePath(string $type, int $id)
    {
        if ($type === 'pemasukan') {
            return DB::table('pemasukan')
                ->where('id_pemasukan', $id)
                ->whereNull('deleted_at')
                ->value('foto_bukti');
        }

        if ($type === 'pengeluaran') {
            return DB::table('pengeluaran')
                ->where('id_pengeluaran', $id)
                ->whereNull('deleted_at')
                ->value('foto_struk');
        }

        return null;
    }

    public function softDelete(string $type, int $id)
    {
        try {
            return DB::transaction(function () use ($type, $id) {
                if ($type === 'pemasukan') {
                    $record = DB::table('pemasukan')
                        ->where('id_pemasukan', $id)
                        ->whereNull('deleted_at')
                        ->lockForUpdate()
                        ->first();

                    if (!$record) {
                        return ['success' => false, 'message' => 'Struk pemasukan tidak ditemukan.'];
                    }

                    DB::table('pemasukan')
                        ->where('transaction_group_id', $record->transaction_group_id)
                        ->whereNull('deleted_at')
                        ->update(['deleted_at' => now(), 'updated_at' => now()]);

                    return ['success' => true];
                }

                if ($type === 'pengeluaran') {
                    $record = DB::table('pengeluaran')
                        ->where('id_pengeluaran', $id)
                        ->whereNull('deleted_at')
                        ->lockForUpdate()
                        ->first();

                    if (!$record) {
                        return ['success' => false, 'message' => 'Struk pengeluaran tidak ditemukan.'];
                    }

                    DB::table('pengeluaran')
                        ->where('transaction_group_id', $record->transaction_group_id)
                        ->whereNull('deleted_at')
                        ->update(['deleted_at' => now(), 'updated_at' => now()]);

                    return ['success' => true];
                }

                return ['success' => false, 'message' => 'Jenis struk tidak valid.'];
            });
        } catch (Throwable $e) {
            return ['success' => false, 'message' => 'Struk gagal dipindahkan ke Recycle Bin.'];
        }
    }

    public function updateFoto(string $type, int $id, string $newPath)
    {
        $oldPath = null;

        try {
            $result = DB::transaction(function () use ($type, $id, $newPath, &$oldPath) {
                if ($type === 'pemasukan') {
                    $record = DB::table('pemasukan')
                        ->where('id_pemasukan', $id)
                        ->whereNull('deleted_at')
                        ->lockForUpdate()
                        ->first();

                    if (!$record) {
                        return ['success' => false, 'message' => 'Struk pemasukan tidak ditemukan.'];
                    }

                    $oldPath = $record->foto_bukti;
                    DB::table('pemasukan')
                        ->where('transaction_group_id', $record->transaction_group_id)
                        ->whereNull('deleted_at')
                        ->update([
                            'foto_bukti' => $newPath,
                            'updated_at' => now(),
                        ]);

                    return ['success' => true];
                }

                if ($type === 'pengeluaran') {
                    $record = DB::table('pengeluaran')
                        ->where('id_pengeluaran', $id)
                        ->whereNull('deleted_at')
                        ->lockForUpdate()
                        ->first();

                    if (!$record) {
                        return ['success' => false, 'message' => 'Struk pengeluaran tidak ditemukan.'];
                    }

                    $oldPath = $record->foto_struk;
                    DB::table('pengeluaran')
                        ->where('transaction_group_id', $record->transaction_group_id)
                        ->whereNull('deleted_at')
                        ->update([
                            'foto_struk' => $newPath,
                            'updated_at' => now(),
                        ]);

                    return ['success' => true];
                }

                return ['success' => false, 'message' => 'Jenis struk tidak valid.'];
            });

            if (!$result['success']) {
                Storage::disk('public')->delete($newPath);
                return $result;
            }

            $this->deleteReceiptIfUnused($oldPath, $newPath);
            return $result;
        } catch (Throwable $e) {
            Storage::disk('public')->delete($newPath);
            return ['success' => false, 'message' => 'Foto struk gagal diperbarui.'];
        }
    }

    private function deleteReceiptIfUnused(?string $oldPath, string $newPath): void
    {
        if (!$oldPath || $oldPath === $newPath) {
            return;
        }

        $stillReferenced = DB::table('pemasukan')->where('foto_bukti', $oldPath)->exists()
            || DB::table('pengeluaran')->where('foto_struk', $oldPath)->exists();

        if (!$stillReferenced) {
            Storage::disk('public')->delete($oldPath);
        }
    }
}
