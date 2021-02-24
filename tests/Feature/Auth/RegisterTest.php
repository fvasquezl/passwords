<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_register()
    {
        $response = $this->postJson(route('api.v1.register'),[
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'device_name' => 'Dispositivo de Faustino',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $token = $response->json('plain-text-token');


        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'The plain text token is invalid'
        );

        $this->assertDatabaseHas('users',[
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
        ]);
    }

    /** @test */
    public function cannot_register_twice()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.register'))
         ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function name_is_required()
    {
        $this->postJson(route('api.v1.register'),[
            'name' => '',
            'email' => 'fvasquez@local.com',
            'device_name' => 'Dispositivo de Faustino',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('name');
    }

    /** @test */
    public function email_is_required()
    {
        $this->postJson(route('api.v1.register'),[
            'name' => 'Faustino Vasquez',
            'email' => '',
            'device_name' => 'Dispositivo de Faustino',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    public function email_must_be_valid()
    {
        $this->postJson(route('api.v1.register'),[
            'name' => 'Faustino Vasquez',
            'email' => 'email',
            'device_name' => 'Dispositivo de Faustino',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    public function email_must_be_unique()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.register'),[
            'name' => 'Faustino Vasquez',
            'email' => $user->email,
            'device_name' => 'Dispositivo de Faustino',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    public function password_is_required()
    {
        $this->postJson(route('api.v1.register'),[
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'device_name' => 'Dispositivo de Faustino',
            'password' => '',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('password');
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        $this->postJson(route('api.v1.register'),[
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'device_name' => 'Dispositivo de Faustino',
            'password' => 'password',
            'password_confirmation' => 'not-confirmed'
        ])->assertJsonValidationErrors('password');
    }

    /** @test */
    public function device_name_is_required()
    {
        $this->postJson(route('api.v1.register'),[
            'name' => 'Faustino Vasquez',
            'email' => 'fvasquez@local.com',
            'device_name' => '',
            'password' => 'password',
            'password_confirmation' => 'password'
        ])->assertJsonValidationErrors('device_name');
    }
}
