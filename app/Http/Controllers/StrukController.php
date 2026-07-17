<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StrukController extends Controller
{
    public function index()
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

        // Get all categories
        $allKategoriPengeluaran = DB::table('jenis_pengeluaran')->where('isAktif', true)->pluck('nama_jenis')->sort();
        $allKategoriPenerimaan = DB::table('jenis_penerimaan')->where('isAktif', true)->pluck('nama_jenis')->sort();
        $allKategori = $allKategoriPengeluaran->merge($allKategoriPenerimaan)->unique()->sort();

        return view('struk', compact('strukList', 'allKategori'));
    }

    public function download(string $type, int $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $path = null;

        if ($type === 'pemasukan') {
            $record = DB::table('pemasukan')->where('id_pemasukan', $id)->whereNull('deleted_at')->first();
            $path = $record?->foto_bukti;
        } elseif ($type === 'pengeluaran') {
            $record = DB::table('pengeluaran')->where('id_pengeluaran', $id)->whereNull('deleted_at')->first();
            $path = $record?->foto_struk;
        }

        if (!$path || !Storage::disk('public')->exists($path)) {
            return redirect()->route('struk')->with('error', 'File struk tidak ditemukan.');
        }

        return Storage::disk('public')->download($path, basename($path));
    }

    /**
     * Soft-delete: kirim ke recycle bin
     */
    public function destroy(Request $request, string $type, int $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

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

        return redirect()->route('struk')->with('success', 'Struk dipindahkan ke Recycle Bin.');
    }

    /**
     * Ganti foto struk
     */
    public function updateFoto(Request $request, string $type, int $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'foto_baru' => 'required|image|max:5120',
        ]);

        $newPath = $request->file('foto_baru')->store('receipts', 'public');

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

        return redirect()->route('struk')->with('success', 'Foto struk berhasil diperbarui.');
    }
}

