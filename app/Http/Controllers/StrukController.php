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
            ->get()
            ->map(function($item) {
                return (object) [
                    'id' => $item->id_pemasukan,
                    'type' => 'Pemasukan',
                    'tanggal' => $item->tanggal,
                    'uraian' => $item->uraian,
                    'nominal' => $item->nominal,
                    'foto' => $item->foto_bukti,
                ];
            });

        $pengeluaran = Pengeluaran::whereNotNull('foto_struk')
            ->where('foto_struk', '!=', '')
            ->whereNull('deleted_at')
            ->get()
            ->map(function($item) {
                return (object) [
                    'id' => $item->id_pengeluaran,
                    'type' => 'Pengeluaran',
                    'tanggal' => $item->tanggal,
                    'uraian' => $item->uraian,
                    'nominal' => $item->nominal,
                    'foto' => $item->foto_struk,
                ];
            });

        $strukList = $pemasukan->concat($pengeluaran)
            ->sortByDesc('tanggal')
            ->values();

        return view('struk', compact('strukList'));
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

