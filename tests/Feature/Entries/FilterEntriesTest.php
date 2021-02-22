<?php

namespace Tests\Feature\Entries;

use App\Models\Category;
use App\Models\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FilterEntriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_filter_entries_by_name()
    {
        Entry::factory()->create([
            'name' => 'My first entry'
        ]);
        Entry::factory()->create([
            'name' => 'Other entry'
        ]);

        $url = route('api.v1.entries.index',['filter[name]'=> 'first']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1,'data')
            ->assertSee('My first entry')
            ->assertDontSee('Other entry');
    }


    /** @test */
    public function can_filter_entries_by_comment()
    {
        Entry::factory()->create([
            'comment' => '<div>My first comment</div>'
        ]);
        Entry::factory()->create([
            'comment' => '<div>Other comment</div>'
        ]);

        $url = route('api.v1.entries.index',['filter[comment]'=> 'first']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1,'data')
            ->assertSee('My first comment')
            ->assertDontSee('Other comment');
    }


    /** @test */
    public function can_filter_entries_by_year()
    {
        Entry::factory()->create([
            'name' => 'My first entry 2020',
            'created_at' => now()->year(2020)
        ]);
        Entry::factory()->create([
            'name' => 'Other entry 2021',
            'created_at' => now()->year(2021)
        ]);

        $url = route('api.v1.entries.index',['filter[year]'=> 2020]);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1,'data')
            ->assertSee('My first entry 2020')
            ->assertDontSee('Other entry 2021');
    }


    /** @test */
    public function can_filter_entries_by_month()
    {
        Entry::factory()->create([
            'name' => 'My first entry january',
            'created_at' => now()->month(1)
        ]);
        Entry::factory()->create([
            'name' => 'Other entry march',
            'created_at' => now()->month(3)
        ]);

        $url = route('api.v1.entries.index',['filter[month]'=> 3]);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1,'data')
            ->assertSee('Other entry march')
            ->assertDontSee('My first entry january');
    }


    /** @test */
    public function cannot_filter_entries_by_unknown_filter()
    {
        Entry::factory()->create();

        $url = route('api.v1.entries.index',['filter[unknown]'=> 3]);

        $this->jsonApi()->get($url)
            ->assertStatus(Response::HTTP_BAD_REQUEST); //400
    }


    /** @test */
    public function can_search_entries_by_name_and_comment()
    {
        Entry::factory()->create([
            'name' => 'Faustino must learn Laravel',
            'comment' => "Learning Laravel"
        ]);
        Entry::factory()->create([
            'name' => 'My second entry',
            'comment' => "already Faustino known Zend"
        ]);

        Entry::factory()->create([
            'name' => 'My third entry',
            'comment' => "Other Comment"
        ]);

        $url = route('api.v1.entries.index',['filter[search]'=> 'Faustino']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2,'data')
            ->assertSee('Faustino must learn Laravel')
            ->assertSee('My second entry')
            ->assertDontSee('My third entry');
    }

    /** @test */
    public function can_search_entries_by_name_and_comment_with_multiple_terms()
    {
        Entry::factory()->create([
            'name' => 'Faustino must learn Laravel',
            'comment' => "Learning.."
        ]);
        Entry::factory()->create([
            'name' => 'My second entry',
            'comment' => "Already Faustino known Zend"
        ]);

        Entry::factory()->create([
            'name' => 'My third entry',
            'comment' => "Learning other things"
        ]);

        $url = route('api.v1.entries.index',['filter[search]'=> 'Learning Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2,'data')
            ->assertSee('Faustino must learn Laravel')
            ->assertSee('My third entry')
            ->assertDontSee('My second entry');
    }

    /** @test */
    public function can_search_entries_by_categories()
    {
        Entry::factory()->times(3)->create();
        $category = Category::factory()->hasEntries(2)->create();

        $this->jsonApi()->filter([
            'categories'=> $category->getRouteKey()
        ])->get(route('api.v1.entries.index'))
            ->assertJsonCount(2,'data')
         ;
    }

    /** @test */
    public function can_search_entries_by_multiple_categories()
    {
        Entry::factory()->times(3)->create();
        $category = Category::factory()->hasEntries(2)->create();
        $category2 = Category::factory()->hasEntries(3)->create();

        $this->jsonApi()->filter([
            'categories'=> $category->getRouteKey().','.$category2->getRouteKey()
        ])->get(route('api.v1.entries.index'))
            ->assertJsonCount(5,'data')
        ;
    }
}
