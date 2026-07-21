<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengeluaranRequest;
use App\Services\PengeluaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class welcomecontroller extends Controller
{
    public function index()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\PengeluaranService::class);
        $expenses = $service->paginate(5);
        $jenis = $service->getJenis();

        return view('welcome', compact('expenses', 'jenis'));
    }


    public function store(StorePengeluaranRequest $request, PengeluaranService $service)
    {
        $validated = $request->validated();
        $result = $service->store($validated, $request->file('receipt_image'));

        if (! $result['success']) {
            return redirect()->route('welcome')->with('error', $result['message']);
        }

        return redirect()->route('welcome')->with('success', 'Semua pengeluaran berhasil disimpan.');
    }


    public function destroy($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\PengeluaranService::class);
        $service->destroy((int) $id);

        $referer = request()->headers->get('referer', '');
        if (str_contains($referer, '/laporan')) {
            return redirect()->route('laporan')->with('success', 'Pengeluaran dihapus.');
        }

        return redirect()->route('welcome')->with('success', 'Pengeluaran dihapus.');
    }

    public function edit($id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $service = app(\App\Services\PengeluaranService::class);
        $group = $service->findGroupById((int) $id);
        if (!$group) {
            return redirect()->route('welcome')->with('error', 'Pengeluaran tidak ditemukan.');
        }

        $expense = $group['base'];
        $expenses = $group['group'];
        $jenis = $service->getJenis();

        return view('pengeluaran_edit', compact('expense', 'expenses', 'jenis'));
    }

    public function update(Request $request, $id)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
        $data = $request->validate([
            'tanggal'               => 'required|date',
            'id_pengeluaran'        => 'array',
            'uraian'                => 'array',
            'uraian.*'              => 'nullable|string|max:255',
            'nominal'               => 'required|array',
            'nominal.*'             => 'required|numeric|min:0',
            'kuantiti'              => 'array',
            'kuantiti.*'            => 'nullable|integer|min:1',
            'id_jenis_pengeluaran'  => 'required|integer',
        ]);

        $service = app(\App\Services\PengeluaranService::class);
        $result = $service->update($data, (int) $id);

        if (! $result['success']) {
            return redirect()->route('pengeluaran.edit', $id)->with('error', $result['message']);
        }

        return redirect()->route('welcome')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

}
