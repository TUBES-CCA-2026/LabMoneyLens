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
<<<<<<< HEAD
            return redirect()->route('pemasukan')->with('error', $result['message']);
        }

        return redirect()->route('pemasukan')->with('success', 'Semua pemasukan berhasil disimpan.');
=======
            return redirect()->route('pemasukan.manual')->with('error', $result['message']);
        }

        return redirect()->route('pemasukan.manual')->with('success', 'Semua pemasukan berhasil disimpan.');
>>>>>>> 0026227 (Baru)
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
<<<<<<< HEAD
            return redirect()->route('pemasukan')->with('error', 'Pemasukan tidak ditemukan.');
=======
            return redirect()->route('pemasukan.manual')->with('error', 'Pemasukan tidak ditemukan.');
>>>>>>> 0026227 (Baru)
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
<<<<<<< HEAD
            return redirect()->route('pemasukan')->with('error', $result['message']);
        }

        return redirect()->route('pemasukan')->with('success', 'Pemasukan berhasil diperbarui.');
=======
            return redirect()->route('pemasukan.edit', ['id' => $id])->with('error', $result['message']);
        }

        return redirect()->route('pemasukan.manual')->with('success', 'Pemasukan berhasil diperbarui.');
>>>>>>> 0026227 (Baru)
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
<<<<<<< HEAD
        $service->destroy((int) $id);

        $referer = request()->headers->get('referer', '');
        if (str_contains($referer, '/laporan')) {
            return redirect()->route('laporan')->with('success', 'Pemasukan dihapus.');
        }

        return redirect()->route('pemasukan')->with('success', 'Pemasukan dihapus.');
=======
        $result = $service->destroy((int) $id);

        $referer = request()->headers->get('referer', '');
        $redirectRoute = str_contains($referer, '/laporan')
            ? 'laporan'
            : 'pemasukan.manual';

        if (! $result['success']) {
            return redirect()->route($redirectRoute)->with('error', $result['message']);
        }

        return redirect()->route($redirectRoute)->with('success', 'Pemasukan berhasil dipindahkan ke Recycle Bin.');
>>>>>>> 0026227 (Baru)
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

    // ========== PEMISAHAN MANUAL vs OTOMATIS ==========

    public function pilih()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }

        if (session('user_role') === 'Kepala Lab') {
            abort(403, 'Unauthorized action.');
        }

        return view('pemasukan_pilih');
    }

    public function showManual()
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

        return view('pemasukan_manual', compact('incomes', 'jenis'));
    }

    public function showOtomatis()
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

        return view('pemasukan_otomatis', compact('incomes', 'jenis'));
    }

    public function storeManual(Request $request, PemasukanService $service)
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
            'kategori_pemasukan'    => 'required|integer',
=======
            'kategori_pemasukan'    => 'required|integer|exists:jenis_penerimaan,id_jenis_penerimaan',
>>>>>>> 0026227 (Baru)
            'uraian'                => 'required|array',
            'uraian.*'              => 'required|string|max:255',
            'nominal'               => 'required|array',
            'nominal.*'             => 'required|numeric|min:1',
            'kuantiti'              => 'array',
            'kuantiti.*'            => 'nullable|integer|min:1',
            'id_jenis_penerimaan'   => 'array',
<<<<<<< HEAD
=======
            'id_jenis_penerimaan.*' => 'nullable|integer|exists:jenis_penerimaan,id_jenis_penerimaan',
>>>>>>> 0026227 (Baru)
            'receipt_image'         => 'nullable|image|max:5120',
        ]);

        $result = $service->storeManual($validated, $request->file('receipt_image'));

        if (!$result['success']) {
            return redirect()->route('pemasukan.manual')->with('error', $result['message']);
        }

        return redirect()->route('pemasukan.manual')->with('success', 'Semua pemasukan berhasil disimpan.');
    }

    public function storeOtomatis(StorePemasukanRequest $request, PemasukanService $service)
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
            return redirect()->route('pemasukan.otomatis')->with('error', $result['message']);
        }

        return redirect()->route('pemasukan.otomatis')->with('success', 'Semua pemasukan berhasil disimpan.');
    }
}
