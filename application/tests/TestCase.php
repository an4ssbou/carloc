<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function actingAs(Authenticatable $user, $guard = null)
    {
        // Each actingAs represents a fresh login; switching accounts must not reuse
        // the preceding account's password snapshot. Sequential requests keep it.
        $this->app['session']->forget('password_hash_'.($guard ?? config('auth.defaults.guard')));

        return parent::actingAs($user, $guard);
    }
}
