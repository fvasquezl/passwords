<?php

namespace Tests\Feature\Entries;

use App\Models\Entry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteEntriesTest extends TestCase
{
    use RefreshDatabase;
    /*
     * Guest Users
     * --- cannot delete entries
     * Auth Users
     * --- can_delete_their_entries
     * --- can_delete_their_entries
     * --- cannot_delete_their_entries_without_permissions
     * --- cannot_delete_other_entries
     */

    /** @test */
    public function guest_users_cannot_delete_entries()
    {
        $entry = Entry::factory()->create();
        $this->jsonApi()
            ->delete(route('api.v1.entries.delete',$entry))
            ->assertStatus(Response::HTTP_UNAUTHORIZED); //401
    }

    /** @test */
    public function auth_users_can_delete_their_entries()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs($entry->user,['entries:delete']);

        $this->jsonApi()
            ->delete(route('api.v1.entries.delete',$entry))
            ->assertStatus(Response::HTTP_NO_CONTENT); //204
    }


    /** @test */
    public function auth_users_cannot_delete_their_entries_without_permissions()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs($entry->user);

        $this->jsonApi()
            ->delete(route('api.v1.entries.delete',$entry))
            ->assertStatus(Response::HTTP_FORBIDDEN); //403
    }


    /** @test */
    public function auth_users_cannot_delete_other_entries()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs(User::factory()->create(),['entries:delete']);

        $this->jsonApi()
            ->delete(route('api.v1.entries.delete',$entry))
            ->assertStatus(Response::HTTP_FORBIDDEN); //403
    }
}
