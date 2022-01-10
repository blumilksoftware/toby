<?php

declare(strict_types=1);

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InertiaTest extends TestCase
{
    public function testInertia(): void
    {
        $response = $this->get("/");

        $response->assertOk();
        $response->assertInertia(fn(Assert $page) => $page->component("Welcome"));
    }
}
