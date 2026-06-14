<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    /**
     * Authenticate the given user against the Sanctum API guard for tests.
     */
    protected function actingAsApi(User $user, array $abilities = ['*']): static
    {
        Sanctum::actingAs($user, $abilities);

        return $this;
    }
}
