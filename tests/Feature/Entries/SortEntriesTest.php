<?php

namespace Tests\Feature\Entries;

use App\Models\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SortEntriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * http://passwords.test/api/v1/entries?sort=name
     */
    public function it_can_sort_entries_by_name_asc()
    {
        Entry::factory()->create(['name' => 'C name']);
        Entry::factory()->create(['name' => 'A name']);
        Entry::factory()->create(['name' => 'B name']);

        $url = route('api.v1.entries.index', ['sort' => 'name']);

        $this->jsonApi()->get($url)->assertSeeInOrder([
            'A name',
            'B name',
            'C name',
        ]);
    }

    /**
     * @test
     *
     * http://passwords.test/api/v1/entries?sort=-name
     */
    public function it_can_sort_entries_by_name_desc()
    {
        Entry::factory()->create(['name' => 'A name']);
        Entry::factory()->create(['name' => 'B name']);
        Entry::factory()->create(['name' => 'C name']);

        $url = route('api.v1.entries.index', ['sort' => '-name']);

        $this->jsonApi()->get($url)
            ->assertSeeInOrder([
                'C name',
                'B name',
                'A name',
            ]);
    }


    /**
     * @test
     *
     * http://passwords.test/api/v1/entries?sort=name,-comment
     * http://passwords.test/api/v1/entries?sort=comment,name
     */
    public function it_can_sort_entries_by_name_asc_and_comment_desc()
    {
        Entry::factory()->create([
            'name' => 'C name',
            'comment' => 'B Comment'
        ]);
        Entry::factory()->create([
            'name' => 'A name',
            'comment' => 'C Comment'
        ]);
        Entry::factory()->create([
            'name' => 'B name',
            'comment' => 'D Comment'
        ]);

        $url = route('api.v1.entries.index') . '?sort=name,-comment';

        $this->jsonApi()->get($url)
            ->assertSeeInOrder([
                'A name',
                'B name',
                'C name',
            ]);
        $url = route('api.v1.entries.index') . '?sort=-comment,name';

        $this->jsonApi()->get($url)->assertSeeInOrder([
            'D Comment',
            'C Comment',
            'B Comment',
        ]);
    }

    /** @test */
    public function it_cannot_sort_entries_by_unknown_fields()
    {
        Entry::factory()->times(3)->create();

        $url = route('api.v1.entries.index') . '?sort=unknown';
        $this->jsonApi()->get($url)->assertStatus(400);
    }
}
