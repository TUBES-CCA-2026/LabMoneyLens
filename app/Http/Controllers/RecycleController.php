<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class RecycleController extends Controller
{
    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\RecycleService::class);
        $data = $service->list();

        return view('recycle', $data);
    }

    public function restore(Request $request, $type, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\RecycleService::class);
        $service->restore($type, $id);

        return redirect()->route('recycle')->with('success', 'Item berhasil dipulihkan.');
    }

    public function forceDelete(Request $request, $type, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\RecycleService::class);
        $service->forceDelete($type, $id);

        return redirect()->route('recycle')->with('success', 'Item berhasil dihapus permanen.');
    }

    public function restoreAll(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\RecycleService::class);
        $service->restoreAll();

        return redirect()->route('recycle')->with('success', 'Semua item berhasil dipulihkan.');
    }

    public function emptyTrash(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\RecycleService::class);
        $service->emptyTrash();

        return redirect()->route('recycle')->with('success', 'Sampah berhasil dikosongkan.');
    }
}
