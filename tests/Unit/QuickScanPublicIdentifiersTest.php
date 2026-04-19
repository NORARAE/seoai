<?php

namespace Tests\Unit;

use App\Models\QuickScan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickScanPublicIdentifiersTest extends TestCase
{
    use RefreshDatabase;

    public function test_quick_scan_assigns_public_scan_identifier_on_create(): void
    {
        $scan = QuickScan::create([
            'email' => 'scan@example.com',
            'url' => 'https://example.com',
            'status' => QuickScan::STATUS_PENDING,
        ]);

        $scan->refresh();

        $this->assertNotNull($scan->public_scan_id);
        $this->assertStringStartsWith('SCAN-', $scan->public_scan_id);
        $this->assertSame($scan->id, QuickScan::idFromPublicReference($scan->publicScanId()));
        $this->assertSame($scan->id, QuickScan::idFromPublicReference($scan->aiScanId()));
        $this->assertSame($scan->id, QuickScan::idFromPublicReference($scan->systemScanId()));
    }
}
