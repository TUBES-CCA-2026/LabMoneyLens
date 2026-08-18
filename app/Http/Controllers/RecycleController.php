<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
=======
use App\Services\RecycleService;
use Illuminate\Http\Request;
>>>>>>> 0026227 (Baru)

class RecycleController extends Controller
{
    public function index()
    {
<<<<<<< HEAD
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\RecycleService::class);
=======
        $service = app(RecycleService::class);
>>>>>>> 0026227 (Baru)
        $data = $service->list();

        return view('recycle', $data);
    }

    public function restore(Request $request, $type, $id)
    {
<<<<<<< HEAD
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\RecycleService::class);
        $service->restore($type, $id);

        return redirect()->route('recycle')->with('success', 'Item berhasil dipulihkan.');
=======
        $service = app(RecycleService::class);
        $result = $service->restore($type, (int) $id);

        return redirect()->route('recycle')
            ->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Transaksi berhasil dipulihkan.' : $result['message']);
>>>>>>> 0026227 (Baru)
    }

    public function forceDelete(Request $request, $type, $id)
    {
<<<<<<< HEAD
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\RecycleService::class);
        $service->forceDelete($type, $id);

        return redirect()->route('recycle')->with('success', 'Item berhasil dihapus permanen.');
=======
        $service = app(RecycleService::class);
        $result = $service->forceDelete($type, (int) $id);

        if ($result['success'] && !empty($result['paths'])) {
            $service->deleteReturnedFiles($result['paths']);
        }

        return redirect()->route('recycle')
            ->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Transaksi berhasil dihapus permanen.' : $result['message']);
>>>>>>> 0026227 (Baru)
    }

    public function restoreAll(Request $request)
    {
<<<<<<< HEAD
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\RecycleService::class);
        $service->restoreAll();

        return redirect()->route('recycle')->with('success', 'Semua item berhasil dipulihkan.');
=======
        $service = app(RecycleService::class);
        $result = $service->restoreAll();

        return redirect()->route('recycle')
            ->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Semua transaksi berhasil dipulihkan.' : ($result['message'] ?? 'Gagal memulihkan transaksi.'));
>>>>>>> 0026227 (Baru)
    }

    public function emptyTrash(Request $request)
    {
<<<<<<< HEAD
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\RecycleService::class);
        $service->emptyTrash();

        return redirect()->route('recycle')->with('success', 'Sampah berhasil dikosongkan.');
=======
        $service = app(RecycleService::class);
        $result = $service->emptyTrash();

        if ($result['success'] && !empty($result['paths'])) {
            $service->deleteReturnedFiles($result['paths']);
        }

        return redirect()->route('recycle')
            ->with($result['success'] ? 'success' : 'error', $result['success'] ? 'Recycle Bin berhasil dikosongkan.' : 'Gagal mengosongkan Recycle Bin.');
>>>>>>> 0026227 (Baru)
    }
}
