<?php

namespace Tests\Feature;

use App\Services\PengeluaranService;
use App\Services\PemasukanService;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private int $incomeCategoryId;
    private int $expenseCategoryId;

    protected function setUp(): void
    {
        parent::setUp();

        $userId = DB::table('users')->insertGetId([
            'nama' => 'Transaction Test User',
            'email' => 'transaction-test@example.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->incomeCategoryId = DB::table('jenis_penerimaan')->insertGetId([
            'nama_jenis' => 'Test Income',
            'isAktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->expenseCategoryId = DB::table('jenis_pengeluaran')->insertGetId([
            'nama_jenis' => 'Test Expense',
            'isAktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session(['user_id' => $userId, 'user_role' => 'Admin']);

        DB::table('pemasukan')->insert([
            'tanggal' => now()->toDateString(),
            'uraian' => 'Saldo awal pengujian',
            'nominal' => 1000,
            'foto_bukti' => null,
            'id_jenis_penerimaan' => $this->incomeCategoryId,
            'id_user' => $userId,
            'is_confirmed' => 1,
            'transaction_group_id' => (string) \Illuminate\Support\Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_multiple_expense_items_share_a_transaction_group_id(): void
    {
        $service = app(PengeluaranService::class);

        $result = $service->store([
            'tanggal' => now()->toDateString(),
            'uraian' => ['Item A', 'Item B'],
            'nominal' => [100, 50],
            'kuantiti' => [2, 1],
            'id_jenis_pengeluaran' => [$this->expenseCategoryId, $this->expenseCategoryId],
        ]);

        $this->assertTrue($result['success']);

        $rows = DB::table('pengeluaran')->orderBy('id_pengeluaran')->get();
        $this->assertCount(2, $rows);
        $this->assertNotNull($rows[0]->transaction_group_id);
        $this->assertSame($rows[0]->transaction_group_id, $rows[1]->transaction_group_id);
        $this->assertSame(200.0, (float) $rows[0]->nominal);
        $this->assertSame(2, (int) $rows[0]->quantity);
        $this->assertSame(50.0, (float) $rows[1]->nominal);
        $this->assertSame(1, (int) $rows[1]->quantity);
    }

    public function test_new_expense_over_available_balance_is_rejected_and_not_saved(): void
    {
        $service = app(PengeluaranService::class);

        $result = $service->store([
            'tanggal' => now()->toDateString(),
            'uraian' => ['Over balance'],
            'nominal' => [1001],
            'kuantiti' => [1],
            'id_jenis_pengeluaran' => [$this->expenseCategoryId],
        ]);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('melebihi saldo', strtolower($result['message']));
        $this->assertSame(0, DB::table('pengeluaran')->count());
    }

    public function test_expense_update_validates_quantity_adjusted_total_before_deleting_items(): void
    {
        $groupId = (string) \Illuminate\Support\Str::uuid();
        $createdAt = now();

        DB::table('pengeluaran')->insert([
            [
                'tanggal' => now()->toDateString(), 'uraian' => 'Item A', 'nominal' => 100,
                'foto_struk' => null, 'id_jenis_pengeluaran' => $this->expenseCategoryId, 'id_user' => session('user_id'),
                'is_confirmed' => 1, 'transaction_group_id' => $groupId, 'created_at' => $createdAt, 'updated_at' => $createdAt,
            ],
            [
                'tanggal' => now()->toDateString(), 'uraian' => 'Item B', 'nominal' => 100,
                'foto_struk' => null, 'id_jenis_pengeluaran' => $this->expenseCategoryId, 'id_user' => session('user_id'),
                'is_confirmed' => 1, 'transaction_group_id' => $groupId, 'created_at' => $createdAt, 'updated_at' => $createdAt,
            ],
        ]);

        $ids = DB::table('pengeluaran')->orderBy('id_pengeluaran')->pluck('id_pengeluaran')->map(fn ($id) => (int) $id)->all();

        $result = app(PengeluaranService::class)->update([
            'tanggal' => now()->toDateString(),
            'id_pengeluaran' => [$ids[0]],
            'uraian' => ['Item A'],
            'nominal' => [600],
            'kuantiti' => [2],
            'id_jenis_pengeluaran' => $this->expenseCategoryId,
        ], $ids[0]);

        $this->assertFalse($result['success']);
        $this->assertSame(100.0, (float) DB::table('pengeluaran')->where('id_pengeluaran', $ids[0])->value('nominal'));
        $this->assertNull(DB::table('pengeluaran')->where('id_pengeluaran', $ids[1])->value('deleted_at'));
    }

    public function test_expense_update_uses_transaction_group_for_new_items(): void
    {
        $groupId = (string) \Illuminate\Support\Str::uuid();
        $createdAt = now();

        $id = DB::table('pengeluaran')->insertGetId([
            'tanggal' => now()->toDateString(), 'uraian' => 'Original', 'nominal' => 100,
            'foto_struk' => null, 'id_jenis_pengeluaran' => $this->expenseCategoryId, 'id_user' => session('user_id'),
            'is_confirmed' => 1, 'transaction_group_id' => $groupId, 'created_at' => $createdAt, 'updated_at' => $createdAt,
        ]);

        $result = app(PengeluaranService::class)->update([
            'tanggal' => now()->toDateString(),
            'id_pengeluaran' => [$id],
            'uraian' => ['Updated'],
            'nominal' => [150],
            'kuantiti' => [2],
            'id_jenis_pengeluaran' => $this->expenseCategoryId,
        ], $id);

        $this->assertTrue($result['success']);
        $row = DB::table('pengeluaran')->where('id_pengeluaran', $id)->first();
        $this->assertSame($groupId, $row->transaction_group_id);
        $this->assertSame(300.0, (float) $row->nominal);
        $this->assertSame(2, (int) $row->quantity);
    }

    public function test_income_update_rejects_negative_projected_balance_and_rolls_back(): void
    {
        $incomeId = DB::table('pemasukan')->orderBy('id_pemasukan')->value('id_pemasukan');

        DB::table('pengeluaran')->insert([
            'tanggal' => now()->toDateString(),
            'uraian' => 'Expense that makes edited income insufficient',
            'nominal' => 1200,
            'quantity' => 1,
            'foto_struk' => null,
            'id_jenis_pengeluaran' => $this->expenseCategoryId,
            'id_user' => session('user_id'),
            'is_confirmed' => 1,
            'transaction_group_id' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $result = app(PemasukanService::class)->update([
            'tanggal' => now()->toDateString(),
            'id_pemasukan' => [$incomeId],
            'uraian' => ['Saldo awal pengujian'],
            'nominal' => [1000],
            'kuantiti' => [1],
            'id_jenis_penerimaan' => $this->incomeCategoryId,
        ], (int) $incomeId);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('negatif', $result['message']);
        $this->assertSame(1000.0, (float) DB::table('pemasukan')->where('id_pemasukan', $incomeId)->value('nominal'));
        $this->assertNull(DB::table('pemasukan')->where('id_pemasukan', $incomeId)->value('deleted_at'));
    }

    public function test_zero_balance_is_allowed_after_income_update(): void
    {
        $incomeId = DB::table('pemasukan')->orderBy('id_pemasukan')->value('id_pemasukan');

        DB::table('pengeluaran')->insert([
            'tanggal' => now()->toDateString(),
            'uraian' => 'Expense equal to income',
            'nominal' => 1000,
            'quantity' => 1,
            'foto_struk' => null,
            'id_jenis_pengeluaran' => $this->expenseCategoryId,
            'id_user' => session('user_id'),
            'is_confirmed' => 1,
            'transaction_group_id' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $result = app(PemasukanService::class)->update([
            'tanggal' => now()->toDateString(),
            'id_pemasukan' => [$incomeId],
            'uraian' => ['Saldo awal pengujian'],
            'nominal' => [1000],
            'kuantiti' => [1],
            'id_jenis_penerimaan' => $this->incomeCategoryId,
        ], (int) $incomeId);

        $this->assertTrue($result['success']);

        $income = DB::table('pemasukan')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $expense = DB::table('pengeluaran')->where('is_confirmed', 1)->whereNull('deleted_at')->sum('nominal');
        $this->assertSame(0.0, (float) $income - (float) $expense);
    }

    public function test_income_delete_is_rejected_when_it_would_make_balance_negative(): void
    {
        $incomeId = DB::table('pemasukan')->orderBy('id_pemasukan')->value('id_pemasukan');

        DB::table('pengeluaran')->insert([
            'tanggal' => now()->toDateString(),
            'uraian' => 'Expense larger than remaining income',
            'nominal' => 1001,
            'quantity' => 1,
            'foto_struk' => null,
            'id_jenis_pengeluaran' => $this->expenseCategoryId,
            'id_user' => session('user_id'),
            'is_confirmed' => 1,
            'transaction_group_id' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $result = app(PemasukanService::class)->destroy((int) $incomeId);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('negatif', $result['message']);
        $this->assertNull(DB::table('pemasukan')->where('id_pemasukan', $incomeId)->value('deleted_at'));
    }

    public function test_expense_restore_is_rejected_when_it_would_make_balance_negative(): void
    {
        $groupId = (string) Str::uuid();
        $deletedAt = now();

        $expenseId = DB::table('pengeluaran')->insertGetId([
            'tanggal' => now()->toDateString(),
            'uraian' => 'Deleted expense too large to restore',
            'nominal' => 1001,
            'quantity' => 1,
            'foto_struk' => null,
            'id_jenis_pengeluaran' => $this->expenseCategoryId,
            'id_user' => session('user_id'),
            'is_confirmed' => 1,
            'transaction_group_id' => $groupId,
            'deleted_at' => $deletedAt,
            'created_at' => $deletedAt,
            'updated_at' => $deletedAt,
        ]);

        $result = app(\App\Services\RecycleService::class)->restore('pengeluaran', $expenseId);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('negatif', $result['message']);
        $this->assertNotNull(DB::table('pengeluaran')->where('id_pengeluaran', $expenseId)->value('deleted_at'));
    }

}
