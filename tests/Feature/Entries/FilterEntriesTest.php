<?php

namespace Tests\Feature\Entries;

use App\Models\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
