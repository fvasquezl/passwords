<?php

namespace Tests\Feature\Auth;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken($user->name)->plainTextToken;

        $this->withHeader('Authorization','Bearer '.$token)
        ->postJson(route('api.v1.logout'))
        ->assertStatus(Response::HTTP_NO_CONTENT); //204


        $this->assertNull(PersonalAccessToken::findToken($token));

    }
}
