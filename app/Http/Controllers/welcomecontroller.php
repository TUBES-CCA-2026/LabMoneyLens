<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengeluaranRequest;
<<<<<<< HEAD
=======
use App\Http\Requests\UpdatePengeluaranRequest;
>>>>>>> 0026227 (Baru)
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

<<<<<<< HEAD
    public function update(Request $request, $id)
=======
    public function update(UpdatePengeluaranRequest $request, $id)
>>>>>>> 0026227 (Baru)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }
<<<<<<< HEAD
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
=======
        $data = $request->validated();
>>>>>>> 0026227 (Baru)

        $service = app(\App\Services\PengeluaranService::class);
        $result = $service->update($data, (int) $id);

        if (! $result['success']) {
<<<<<<< HEAD
            return redirect()->route('pengeluaran.edit', $id)->with('error', $result['message']);
=======
            return redirect()->route('pengeluaran.edit', $id)->withInput()->with('error', $result['message']);
>>>>>>> 0026227 (Baru)
        }

        return redirect()->route('welcome')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    // ========== PEMISAHAN MANUAL vs OTOMATIS ==========

    public function pilih()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        return view('pengeluaran_pilih');
    }

    public function showManual()
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

        return view('pengeluaran_manual', compact('expenses', 'jenis'));
    }

    public function showOtomatis()
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

        return view('pengeluaran_otomatis', compact('expenses', 'jenis'));
    }

    public function storeManual(Request $request, PengeluaranService $service)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'tanggal'               => 'required|date',
<<<<<<< HEAD
            'kategori_pengeluaran'  => 'required|integer',
=======
            'kategori_pengeluaran'  => 'required|integer|exists:jenis_pengeluaran,id_jenis_pengeluaran',
>>>>>>> 0026227 (Baru)
            'uraian'                => 'required|array',
            'uraian.*'              => 'required|string|max:255',
            'nominal'               => 'required|array',
            'nominal.*'             => 'required|numeric|min:1',
            'kuantiti'              => 'array',
            'kuantiti.*'            => 'nullable|integer|min:1',
            'id_jenis_pengeluaran'  => 'array',
<<<<<<< HEAD
=======
            'id_jenis_pengeluaran.*' => 'nullable|integer|exists:jenis_pengeluaran,id_jenis_pengeluaran',
>>>>>>> 0026227 (Baru)
            'receipt_image'         => 'nullable|image|max:5120',
        ]);

        $result = $service->storeManual($validated, $request->file('receipt_image'));

        if (!$result['success']) {
            return redirect()->route('pengeluaran.manual')->with('error', $result['message']);
        }

        return redirect()->route('pengeluaran.manual')->with('success', 'Semua pengeluaran berhasil disimpan.');
    }

    public function storeOtomatis(StorePengeluaranRequest $request, PengeluaranService $service)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();
        $result = $service->store($validated, $request->file('receipt_image'));

        if (!$result['success']) {
            return redirect()->route('pengeluaran.otomatis')->with('error', $result['message']);
        }

        return redirect()->route('pengeluaran.otomatis')->with('success', 'Semua pengeluaran berhasil disimpan.');
    }

}
