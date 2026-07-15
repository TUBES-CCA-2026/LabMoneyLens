<?php

namespace Tests\Feature;

use Tests\TestCase;

class LaporanDownloadTest extends TestCase
{
    public function test_download_is_blocked_when_report_has_no_data(): void
    {
        $response = $this->withSession([
            'user_id' => 1,
            'user_name' => 'Tester',
            'user_role' => 'Administrator',
        ])->get('/laporan?export=csv');

        $response->assertRedirect('/laporan');
    }
}
