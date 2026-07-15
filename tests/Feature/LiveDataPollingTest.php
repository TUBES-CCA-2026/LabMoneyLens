<?php

namespace Tests\Feature;

use Tests\TestCase;

class LiveDataPollingTest extends TestCase
{
    public function test_live_data_endpoint_returns_json_summary(): void
    {
        $response = $this->withSession([
            'user_id' => 1,
            'user_name' => 'Tester',
            'user_role' => 'Administrator',
        ])->get('/dashboard/live-data');

        $response->assertOk();
        $response->assertJsonStructure([
            'totalIncome',
            'totalExpense',
            'balance',
            'recentTransactions',
        ]);
    }
}
