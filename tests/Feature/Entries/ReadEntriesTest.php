<?php

namespace Tests\Feature\Entries;

use App\Models\Entry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadEntriesTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_fetch_single_entry()
    {
        $entry = Entry::factory()->create();

        $response = $this->jsonApi()->get(route('api.v1.entries.read', $entry));

        $response->assertJson([
            'data' => [
                'type' => 'entries',
                'id' =>(string) $entry->getRouteKey(),
                'attributes' => [
                    'name' => $entry->name,
                    'slug' => $entry->slug,
                    'username' => $entry->username,
                    'password' => $entry->password,
                    'url' => $entry->url,
                    'comment' => $entry->comment,
                    'createdAt' => $entry->created_at->toAtomString(),
                    'updatedAt' => $entry->updated_at->toAtomString(),
                ],
                'links' => [
                    'self' => route('api.v1.entries.read', $entry)
                ]
            ]
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
                    'id' =>(string) $entry[0]->getRouteKey(),
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
                    'id' =>(string) $entry[1]->getRouteKey(),
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
                    'id' =>(string) $entry[2]->getRouteKey(),
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
