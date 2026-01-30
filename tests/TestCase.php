<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (!app()->bound('hash')) {
            $this->refreshApplication();
        }

        // Run migrations for tests
        $this->artisan('migrate:fresh');

        // Optionally seed test data
//        $this->artisan('db:seed');
    }
}
