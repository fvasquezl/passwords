<?php

namespace Tests\Feature\Entries;

use App\Models\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncludeCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_categories()
    {
        $entry = Entry::factory()->create();

        $this->jsonApi()
            ->includePaths('categories')
            ->get(route('api.v1.entries.read',$entry))
            ->assertSee($entry->category->name)
            ->assertJsonFragment([
                'related' => route('api.v1.entries.relationships.categories',$entry)
            ])
            ->assertJsonFragment([
                'self' => route('api.v1.entries.relationships.categories.read',$entry)
            ]);
    }



    /** @test */
    public function can_include_related_categories()
    {
        $entry = Entry::factory()->create();

        $this->jsonApi()
            ->get(route('api.v1.entries.relationships.categories',$entry))
            ->assertSee($entry->category->name);

        $this->jsonApi()
            ->get(route('api.v1.entries.relationships.categories.read',$entry))
            ->assertSee($entry->category->getRouteKey());

    }
}
