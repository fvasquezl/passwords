<?php

namespace Tests\Feature\Entries;

use App\Models\Category;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UpdateEntriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  Update Entries
     *
     * 'guest user'
     *  --- Cannot update an entry
     * 'auth user'
     *  --- Can update their entries
     * 'auth user'
     *  --- Cannot update an entry without permissions
     * 'auth user'
     *  --- Cannot update an entry on behalf another user
     *
     **/

    /** @test */
    public function guest_user_cannot_update_an_entry()
    {
        $entry = Entry::factory()->create();

        $this->jsonApi()->patch(route('api.v1.entries.update',$entry))
            ->assertStatus(Response::HTTP_UNAUTHORIZED); //401
    }


    /** @test */
    public function auth_users_can_update_their_entries()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs($entry->user);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'id' => $entry->getRouteKey(),
            'attributes' => [
                'name' => 'name Changed',
                'slug' => 'name-changed',
                'username' => 'username Changed',
                'password' => 'passwordChanged',
                'url' => 'http://url-changed.com',
                'comment' => 'comment Changed',
            ]
        ])->patch(route('api.v1.entries.update',$entry))
            ->assertStatus(Response::HTTP_OK); //200

        $this->assertDatabaseHas('entries', [
            'name' => 'name Changed',
            'slug' => 'name-changed',
            'username' => 'username Changed',
            'password' => 'passwordChanged',
            'url' => 'http://url-changed.com',
            'comment' => 'comment Changed',
        ]);
    }



    /** @test */
    public function auth_users_cannot_update_an_entry_without_permissions()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs($entry->user);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'id' => $entry->getRouteKey(),
            'attributes' => [
                'name' => 'name Updated',
                'slug' => 'name-updated',
                'username' => 'username Updated',
                'password' => 'passwordUpdated',
                'url' => 'http://url-updated.com',
                'comment' => 'comment Updated',
            ]
        ])->patch(route('api.v1.entries.update',$entry))
            ->assertStatus(Response::HTTP_OK); //200

        $this->assertDatabaseHas('entries', [
            'name' => 'name Updated',
            'slug' => 'name-updated',
            'username' => 'username Updated',
            'password' => 'passwordUpdated',
            'url' => 'http://url-updated.com',
            'comment' => 'comment Updated',
        ]);
    }


    /** @test */
    public function auth_users_cannot_update_others_entries()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'entries',
            'id' => $entry->getRouteKey(),
            'attributes' => [
                'name' => 'name Updated',
                'slug' => 'name-updated',
                'username' => 'username Updated',
                'password' => 'passwordUpdated',
                'url' => 'http://url-updated.com',
                'comment' => 'comment Updated',
            ]
        ])->patch(route('api.v1.entries.update',$entry))
            ->assertStatusCode(Response::HTTP_FORBIDDEN); //403

        $this->assertDatabaseMissing('entries', [
            'name' => 'name Updated',
            'slug' => 'name-updated',
            'username' => 'username Updated',
            'password' => 'passwordUpdated',
            'url' => 'http://url-updated.com',
            'comment' => 'comment Updated',
        ]);
    }
}


