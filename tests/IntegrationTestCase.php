<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class IntegrationTestCase extends BaseTestCase
{
    // No RefreshDatabase trait here!
    // We assume the database is already migrated and persistent.
}
