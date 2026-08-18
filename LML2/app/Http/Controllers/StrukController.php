<?php

namespace App\Http\Controllers;

use App\Http\Requests\StrukUpdateFotoRequest;
use Illuminate\Http\Request;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StrukController extends Controller
{
    public function index()
    {
        $service = app(\App\Services\StrukService::class);
        $data = $service->list();

        return view('struk', $data);
    }

    public function download(string $type, int $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        $path = null;
        $service = app(\App\Services\StrukService::class);
        $path = $service->resolvePath($type, $id);

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

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\StrukService::class);
        $result = $service->softDelete($type, $id);

        return redirect()->route('struk')
            ->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Transaksi struk dipindahkan ke Recycle Bin.' : $result['message']);
    }

    /**
     * Ganti foto struk
     */
    public function updateFoto(StrukUpdateFotoRequest $request, string $type, int $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        $newPath = $request->file('foto_baru')->store('receipts', 'public');

        $service = app(\App\Services\StrukService::class);
        $result = $service->updateFoto($type, $id, $newPath);

        return redirect()->route('struk')
            ->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Foto struk seluruh item transaksi berhasil diperbarui.' : $result['message']);
    }
}

