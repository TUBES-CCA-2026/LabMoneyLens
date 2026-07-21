<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;

class StrukService
{
    public function list()
    {
        $pemasukan = Pemasukan::whereNotNull('foto_bukti')
            ->where('foto_bukti', '!=', '')
            ->whereNull('deleted_at')
            ->with('jenisPenerimaan')
            ->get()
            ->groupBy('foto_bukti')
            ->map(function($group) {
                $first = $group->first();
                $count = $group->count();
                $uraian = $count > 1 ? $count . ' Item (Termasuk: ' . $first->uraian . ')' : $first->uraian;
                return (object) [
                    'id' => $first->id_pemasukan,
                    'type' => 'Pemasukan',
                    'kategori' => $count > 1 ? 'Gabungan Kategori' : ($first->jenisPenerimaan?->nama_jenis ?? 'Lainnya'),
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
            ->groupBy('foto_struk')
            ->map(function($group) {
                $first = $group->first();
                $count = $group->count();
                $uraian = $count > 1 ? $count . ' Item (Termasuk: ' . $first->uraian . ')' : $first->uraian;
                return (object) [
                    'id' => $first->id_pengeluaran,
                    'type' => 'Pengeluaran',
                    'kategori' => $count > 1 ? 'Gabungan Kategori' : ($first->jenisPengeluaran?->nama_jenis ?? 'Lainnya'),
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
        $path = null;

        if ($type === 'pemasukan') {
            $record = DB::table('pemasukan')->where('id_pemasukan', $id)->whereNull('deleted_at')->first();
            $path = $record?->foto_bukti;
        } elseif ($type === 'pengeluaran') {
            $record = DB::table('pengeluaran')->where('id_pengeluaran', $id)->whereNull('deleted_at')->first();
            $path = $record?->foto_struk;
        }

        return $path;
    }

    public function softDelete(string $type, int $id)
    {
        if ($type === 'pemasukan') {
            DB::table('pemasukan')
                ->where('id_pemasukan', $id)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);
        } elseif ($type === 'pengeluaran') {
            DB::table('pengeluaran')
                ->where('id_pengeluaran', $id)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => now()]);
        }

        return ['success' => true];
    }

    public function updateFoto(string $type, int $id, string $newPath)
    {
        if ($type === 'pemasukan') {
            $record = DB::table('pemasukan')->where('id_pemasukan', $id)->whereNull('deleted_at')->first();
            if ($record && $record->foto_bukti) {
                Storage::disk('public')->delete($record->foto_bukti);
            }
            DB::table('pemasukan')->where('id_pemasukan', $id)->update([
                'foto_bukti' => $newPath,
                'updated_at' => now(),
            ]);
        } elseif ($type === 'pengeluaran') {
            $record = DB::table('pengeluaran')->where('id_pengeluaran', $id)->whereNull('deleted_at')->first();
            if ($record && $record->foto_struk) {
                Storage::disk('public')->delete($record->foto_struk);
            }
            DB::table('pengeluaran')->where('id_pengeluaran', $id)->update([
                'foto_struk' => $newPath,
                'updated_at' => now(),
            ]);
        }

        return ['success' => true];
    }
}
