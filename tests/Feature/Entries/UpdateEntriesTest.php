<?php

namespace Tests\Feature\Entries;

use App\Models\Category;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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
     *  --- Cannot update their entries without permissions
     * 'auth user'
     *  --- Cannot update other entries
     * 'auth user'
     *  --- Can update only name
     *  --- Can update only slug
     *  --- Can update only username
     *  --- Can update only password
     *  --- Can update only url
     *  --- Can update only comment
     *
     * Related data
     * 'auth user'
     *  --- Can replace the category
     *  --- can replace the authors
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

        Sanctum::actingAs($entry->user,['entries:update']);

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
    public function auth_users_cannot_update_their_entries_without_permissions()
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
            ->assertStatus(Response::HTTP_FORBIDDEN); //403

        $this->assertDatabaseMissing('entries', [
            'name' => 'name Updated',
            'slug' => 'name-updated',
            'username' => 'username Updated',
            'password' => 'passwordUpdated',
            'url' => 'http://url-updated.com',
            'comment' => 'comment Updated',
        ]);
    }

    /** @test */
    public function auth_users_cannot_update_other_entries()
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

    /** @test */
    public function can_update_only_name()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs($entry->user,['entries:update']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'id' => $entry->getRouteKey(),
            'attributes' => [
                'name' => 'name Changed',
            ]
        ])->patch(route('api.v1.entries.update',$entry))
            ->assertStatus(Response::HTTP_OK); //200

        $this->assertDatabaseHas('entries', [
            'name' => 'name Changed',
        ]);
    }

    /** @test */
    public function can_update_only_slug()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs($entry->user,['entries:update']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'id' => $entry->getRouteKey(),
            'attributes' => [
                'slug' => 'name-changed',
            ]
        ])->patch(route('api.v1.entries.update',$entry))
            ->assertStatus(Response::HTTP_OK); //200

        $this->assertDatabaseHas('entries', [
            'slug' => 'name-changed',
        ]);
    }

    /** @test */
    public function can_update_only_username()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs($entry->user,['entries:update']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'id' => $entry->getRouteKey(),
            'attributes' => [
                'username' => 'username Changed',
            ]
        ])->patch(route('api.v1.entries.update',$entry))
            ->assertStatus(Response::HTTP_OK); //200

        $this->assertDatabaseHas('entries', [
            'username' => 'username Changed',
        ]);
    }

    /** @test */
    public function can_update_only_password()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs($entry->user,['entries:update']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'id' => $entry->getRouteKey(),
            'attributes' => [
                'password' => 'passwordChanged',
            ]
        ])->patch(route('api.v1.entries.update',$entry))
            ->assertStatus(Response::HTTP_OK); //200

        $this->assertDatabaseHas('entries', [
            'password' => 'passwordChanged',
        ]);
    }

    /** @test */
    public function can_update_only_url()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs($entry->user,['entries:update']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'id' => $entry->getRouteKey(),
            'attributes' => [
                'url' => 'http://url-changed.com',
            ]
        ])->patch(route('api.v1.entries.update',$entry))
            ->assertStatus(Response::HTTP_OK); //200

        $this->assertDatabaseHas('entries', [
            'url' => 'http://url-changed.com',
        ]);
    }

    /** @test */
    public function can_update_only_comment()
    {
        $entry = Entry::factory()->create();

        Sanctum::actingAs($entry->user,['entries:update']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'id' => $entry->getRouteKey(),
            'attributes' => [
                'comment' => 'comment Changed',
            ]
        ])->patch(route('api.v1.entries.update',$entry))
            ->assertStatus(Response::HTTP_OK); //200

        $this->assertDatabaseHas('entries', [
            'comment' => 'comment Changed',
        ]);
    }

    /** @test */
    public function can_replace_the_category()
    {
        $entry = Entry::factory()->create(); //Entry has his own category

        $category = Category::factory()->create(); //New Category

        Sanctum::actingAs($entry->user,['entries:modify-categories']);

        $this->jsonApi()->withData([
            'type' => 'categories',
            'id' => $category->getRouteKey(),
        ])->patch(route('api.v1.entries.relationships.categories.replace',$entry))
            ->assertStatus(Response::HTTP_NO_CONTENT); //204

        $this->assertDatabaseHas('entries', [
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function can_replace_the_authors()
    {
        $entry = Entry::factory()->create();
        $author = User::factory()->create();

        Sanctum::actingAs($entry->user,['entries:modify-authors']);

        $this->jsonApi()->withData([
            'type' => 'authors',
            'id' => $author->getRouteKey(),
        ])->patch(route('api.v1.entries.relationships.authors.replace',$entry))
            ->assertStatus(Response::HTTP_NO_CONTENT); //204

        $this->assertDatabaseHas('entries', [
            'user_id' => $author->id
        ]);
    }

}


