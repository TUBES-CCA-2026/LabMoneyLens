<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePemasukanRequest;
use App\Services\PemasukanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemasukanController extends Controller
{
    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\PemasukanService::class);
        $incomes = $service->paginate(5);
        $jenis = $service->getJenis();

        return view('pemasukan', compact('incomes', 'jenis'));
    }

    public function store(StorePemasukanRequest $request, PemasukanService $service)
    {
        $validated = $request->validated();
        $result = $service->store($validated, $request->file('receipt_image'));

        if (! $result['success']) {
            return redirect()->route('pemasukan')->with('error', $result['message']);
        }

        return redirect()->route('pemasukan')->with('success', 'Semua pemasukan berhasil disimpan.');
    }

    public function edit($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\PemasukanService::class);
        $group = $service->findGroupById((int) $id);
        if (!$group) {
            return redirect()->route('pemasukan')->with('error', 'Pemasukan tidak ditemukan.');
        }

        $income = $group['base'];
        $incomes = $group['group'];
        $jenis = $service->getJenis();

        return view('pemasukan_edit', compact('income', 'incomes', 'jenis'));
    }

    public function update(\App\Http\Requests\UpdatePemasukanRequest $request, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $data = $request->validated();

        $service = app(\App\Services\PemasukanService::class);
        $result = $service->update($data, (int) $id);

        if (! $result['success']) {
            return redirect()->route('pemasukan')->with('error', $result['message']);
        }

        return redirect()->route('pemasukan')->with('success', 'Pemasukan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\PemasukanService::class);
        $service->destroy((int) $id);

        $referer = request()->headers->get('referer', '');
        if (str_contains($referer, '/laporan')) {
            return redirect()->route('laporan')->with('success', 'Pemasukan dihapus.');
        }

        return redirect()->route('pemasukan')->with('success', 'Pemasukan dihapus.');
    }

    public function storeKategori(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        if (session('user_role') === 'Kepala Lab') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $data = $request->validate([
            'nama_jenis' => 'required|string|max:255',
        ]);

        $service = app(\App\Services\PemasukanService::class);
        $result = $service->storeKategori($data);

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']]);
        }

        return response()->json([
            'success' => true,
            'id' => $result['id'],
            'nama' => $result['nama'],
        ]);
    }
}
