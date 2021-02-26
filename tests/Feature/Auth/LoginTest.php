<?php

namespace Tests\Feature\Auth;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_login_with_valid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'iPhone de ' . $user->name,
        ]);

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'The plain text token is invalid'
        );


    }



    /** @test */
    public function can_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'iPhone de ' . $user->name,
        ]);



        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            'The plain text token is invalid'
        );
    }



    /** @test */
    public function cannot_login_twice()
    {
        $user = User::factory()->create();

        $token = $user->createToken($user->name)->plainTextToken;

        $this->withHeader('Authorization','Bearer '.$token)
            ->postJson(route('api.v1.login'))
            ->assertStatus(Response::HTTP_NO_CONTENT); //204

    }

    /** @test */
    public function user_permissions_are_assigned_as_abilities_to_the_token_response()
    {
        $user = User::factory()->create();

        $permissions = Permission::factory()->create([
            'name' => $entriesCreatePermissions = 'entries:create'
        ]);
        $permissions2 = Permission::factory()->create([
            'name' => $entriesUpdatePermissions = 'entries:update'
        ]);

      $user->givePermissionTo($permissions);
      $user->givePermissionTo($permissions2);


      $response = $this->postJson(route('api.v1.login'),[
          'email' => $user->email,
          'password' => 'password',
          'device_name' => 'iPhone de ' . $user->name
      ]);

        $dbToken = PersonalAccessToken::findToken(
            $response->json('plain-text-token')
        );

        $this->assertTrue($dbToken->can($entriesCreatePermissions));
        $this->assertTrue($dbToken->can($entriesUpdatePermissions));
        $this->assertFalse($dbToken->can('entries:delete'));

    }



    /** @test */
    public function email_is_required()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => '',
            'password' => 'wrong-password',
            'device_name' => 'iPhone de  fvasquez'
        ])->assertSee(__('validation.required',['attribute'=>'email']))
            ->assertJsonValidationErrors('email');

    }

    /** @test */
    public function email_must_be_valid()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'invalid',
            'password' => 'password',
            'device_name' => 'iPhone de  fvasquez'
        ])->assertSee(__('validation.email',['attribute'=>'email']))
            ->assertJsonValidationErrors('email');

    }

    /** @test */
    public function password_is_required()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'fvasquez@local.com',
            'password' => '',
            'device_name' => 'iPhone de  fvasquez'
        ])->assertJsonValidationErrors('password');
    }


    /** @test */
    public function device_name_is_required()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'fvasquez@local.com',
            'password' => 'password',
            'device_name' => ''
        ])->assertJsonValidationErrors('device_name');
    }
}
