<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config([
            'queue.order_connection' => 'sync',
            'queue.live_chat_connection' => 'sync',
        ]);
        Cache::flush();
    }
}
