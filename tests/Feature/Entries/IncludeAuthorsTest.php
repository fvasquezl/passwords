<?php

namespace Tests\Feature\Entries;

use App\Models\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncludeAuthorsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_authors()
    {
        $entry = Entry::factory()->create();

        $this->jsonApi()
            ->includePaths('authors')
            ->get(route('api.v1.entries.read',$entry))
            ->assertSee($entry->user->name)
            ->assertJsonFragment([
                'related' => route('api.v1.entries.relationships.authors',$entry)
            ])
            ->assertJsonFragment([
                'self' => route('api.v1.entries.relationships.authors.read',$entry)
            ]);
    }



    /** @test */
    public function can_include_related_authors()
    {
        $entry = Entry::factory()->create();

        $this->jsonApi()
            ->get(route('api.v1.entries.relationships.authors',$entry))
            ->assertSee($entry->user->name);

        $this->jsonApi()
             ->get(route('api.v1.entries.relationships.authors.read',$entry))
             ->assertSee($entry->user->id);

    }
}
