<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncludeEntriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_articles()
    {
        $category = Category::factory()->hasEntries()->create();

        $this->jsonApi()
            ->includePaths('entries')
            ->get(route('api.v1.categories.read',$category))
            ->assertJsonFragment([
                'related' => route('api.v1.categories.relationships.entries',$category)
            ])
            ->assertJsonFragment([
                'self' => route('api.v1.categories.relationships.entries.read',$category)
            ]);
    }

    /** @test */
    public function can_fetch_related_entries()
    {
        $category =Category::factory()
            ->hasEntries()
            ->create();

        $this->jsonApi()
            ->get(route('api.v1.categories.relationships.entries',$category))
            ->assertSee($category->entries[0]->name)
        ;
        $this->jsonApi()
            ->get(route('api.v1.categories.relationships.entries.read',$category))
            ->assertSee($category->entries[0]->getRouteKey())
        ;

    }
}
