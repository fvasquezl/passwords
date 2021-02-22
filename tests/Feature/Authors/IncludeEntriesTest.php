<?php

namespace Tests\Feature\Authors;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncludeEntriesTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_include_entries()
    {
        $author = User::factory()->hasEntries()->create();

        $this->jsonApi()
            ->includePaths('entries')
            ->get(route('api.v1.authors.read',$author))
            ->assertSee($author->entries[0]->name)
            ->assertJsonFragment([
                'related' => route('api.v1.authors.relationships.entries',$author)
            ])
            ->assertJsonFragment([
                'self' => route('api.v1.authors.relationships.entries.read',$author)
            ]);
    }



    /** @test */
    public function can_fetch_related_entries()
    {
        $author = User::factory()
            ->hasEntries()
            ->create();

        $this->jsonApi()
            ->get(route('api.v1.authors.relationships.entries',$author))
            ->assertSee($author->entries[0]->name);

        $this->jsonApi()
            ->get(route('api.v1.authors.relationships.entries.read',$author))
            ->assertSee($author->entries[0]->getRouteKey());

    }
}
