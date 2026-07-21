<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Mendapatkan range tanggal berdasarkan semester.
     * Sem 1 = Januari – Juni, Sem 2 = Juli – Desember.
     */
    private function getSemesterRange(int $year, int $semester): array
    {
        if ($semester === 1) {
            return [
                'start' => Carbon::create($year, 1, 1)->startOfDay(),
                'end'   => Carbon::create($year, 6, 30)->endOfDay(),
            ];
        }
        return [
            'start' => Carbon::create($year, 7, 1)->startOfDay(),
            'end'   => Carbon::create($year, 12, 31)->endOfDay(),
        ];
    }

    public function index(Request $request)
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login');
        }
        $service = app(\App\Services\DashboardService::class);
        $data = $service->getDashboardData($request);

        return view('dashboard', $data);
    }

    /**
     * AJAX endpoint: kembalikan data chart pengeluaran per semester.
     */
    public function chartData(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $service = app(\App\Services\DashboardService::class);
        $result = $service->chartData($request);

        return response()->json([
            'categories' => $result['expenseCategories'],
            'year'       => $result['year'],
            'semester'   => $result['semester'],
        ]);
    }

    public function liveData(Request $request)
    {
        if (!session()->has('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $service = app(\App\Services\DashboardService::class);
        $result = $service->liveData();

        return response()->json([
            'totalIncome' => (float) $result['totalIncome'],
            'totalExpense' => (float) $result['totalExpense'],
            'balance' => (float) $result['balance'],
            'recentTransactions' => $result['recentTransactions'],
        ]);
    }
}
