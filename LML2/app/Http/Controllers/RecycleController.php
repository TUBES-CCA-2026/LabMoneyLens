<?php

namespace App\Http\Controllers;

use App\Services\RecycleService;
use Illuminate\Http\Request;

class RecycleController extends Controller
{
    public function index()
    {
        $service = app(RecycleService::class);
        $data = $service->list();

        return view('recycle', $data);
    }

    public function restore(Request $request, $type, $id)
    {
        $service = app(RecycleService::class);
        $result = $service->restore($type, (int) $id);

        return redirect()->route('recycle')
            ->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Transaksi berhasil dipulihkan.' : $result['message']);
    }

    public function forceDelete(Request $request, $type, $id)
    {
        $service = app(RecycleService::class);
        $result = $service->forceDelete($type, (int) $id);

        if ($result['success'] && !empty($result['paths'])) {
            $service->deleteReturnedFiles($result['paths']);
        }

        return redirect()->route('recycle')
            ->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Transaksi berhasil dihapus permanen.' : $result['message']);
    }

    public function restoreAll(Request $request)
    {
        $service = app(RecycleService::class);
        $result = $service->restoreAll();

        return redirect()->route('recycle')
            ->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Semua transaksi berhasil dipulihkan.' : ($result['message'] ?? 'Gagal memulihkan transaksi.'));
    }

    public function emptyTrash(Request $request)
    {
        $service = app(RecycleService::class);
        $result = $service->emptyTrash();

        if ($result['success'] && !empty($result['paths'])) {
            $service->deleteReturnedFiles($result['paths']);
        }

        return redirect()->route('recycle')
            ->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Recycle Bin berhasil dikosongkan.' : 'Gagal mengosongkan Recycle Bin.');
    }
}
