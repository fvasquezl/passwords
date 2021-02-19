<?php

namespace Tests\Feature\Entries;

use App\Models\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginateEntriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * http://passwords.test/api/v1/entries?page[size]=2&page[number]=3
     *
     */
    public function can_fetch_paginate_entries()
    {
        Entry::factory()->times(10)->create();
        $url = route('api.v1.entries.index', ['page[size]' => 2, 'page[number]' => 3]);

        $response = $this->jsonApi()->get($url);

        $response->assertJsonStructure([
            'links' => ['first', 'prev', 'next', 'last',]
        ]);

        $response->assertJsonFragment([
            'first' => route('api.v1.entries.index', ['page[number]' => 1, 'page[size]' => 2]),
            'prev' => route('api.v1.entries.index', ['page[number]' => 2, 'page[size]' => 2]),
            'next' => route('api.v1.entries.index', ['page[number]' => 4, 'page[size]' => 2]),
            'last' => route('api.v1.entries.index', ['page[number]' => 5, 'page[size]' => 2]),
        ]);
    }


}
