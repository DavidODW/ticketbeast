<?php

namespace Tests\Unit\Http\Middleware;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Middleware\ForceStripeAccount;

class ForceStripeAccountTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function users_without_a_stripe_account_are_forced_to_connect_with_stripe()
    {
        $this->be(factory(User::class)->create([
            'stripe_account_id' => null
        ]));

        $middleware = new ForceStripeAccount;

        $response = $middleware->handle(new Request, function ($request) {
            $this->fail("Next middleware was called when it should not have been.");
        });

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('backstage.stripe-connect.connect'), $response->getTargetUrl());
    }
}