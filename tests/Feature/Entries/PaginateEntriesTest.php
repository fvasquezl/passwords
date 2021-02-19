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


    /** @test */
    public function can_fetch_all_entries()
    {
        $entry = Entry::factory()->times(3)->create();

        $response = $this->jsonApi()->get(route('api.v1.entries.index'));

        $response->assertJson([
            'data' => [
                [
                    'type' => 'entries',
                    'id' => (string)$entry[0]->getRouteKey(),
                    'attributes' => [
                        'name' => $entry[0]->name,
                        'slug' => $entry[0]->slug,
                        'username' => $entry[0]->username,
                        'password' => $entry[0]->password,
                        'url' => $entry[0]->url,
                        'comment' => $entry[0]->comment,
                        'createdAt' => $entry[0]->created_at->toAtomString(),
                        'updatedAt' => $entry[0]->updated_at->toAtomString(),
                    ],
                    'links' => [
                        'self' => route('api.v1.entries.read', $entry[0])
                    ]
                ],
                [
                    'type' => 'entries',
                    'id' => (string)$entry[1]->getRouteKey(),
                    'attributes' => [
                        'name' => $entry[1]->name,
                        'slug' => $entry[1]->slug,
                        'username' => $entry[1]->username,
                        'password' => $entry[1]->password,
                        'url' => $entry[1]->url,
                        'comment' => $entry[1]->comment,
                        'createdAt' => $entry[1]->created_at->toAtomString(),
                        'updatedAt' => $entry[1]->updated_at->toAtomString(),
                    ],
                    'links' => [
                        'self' => route('api.v1.entries.read', $entry[1])
                    ]
                ],
                [
                    'type' => 'entries',
                    'id' => (string)$entry[2]->getRouteKey(),
                    'attributes' => [
                        'name' => $entry[2]->name,
                        'slug' => $entry[2]->slug,
                        'username' => $entry[2]->username,
                        'password' => $entry[2]->password,
                        'url' => $entry[2]->url,
                        'comment' => $entry[2]->comment,
                        'createdAt' => $entry[2]->created_at->toAtomString(),
                        'updatedAt' => $entry[2]->updated_at->toAtomString(),
                    ],
                    'links' => [
                        'self' => route('api.v1.entries.read', $entry[2])
                    ]
                ],
            ]
        ]);
    }
}
