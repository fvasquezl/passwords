<?php

namespace Tests\Feature\Entries;

use App\Models\Category;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateEntryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    /** @test */
    public function auth_user_can_create_an_entry()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $entry = array_filter(Entry::factory()->raw([
            'category_id' => null,
            'user_id' => null
        ]));


        $this->assertDatabaseMissing('entries',$entry);

        Sanctum::actingAs($user,['entries:create']);

        $this->jsonApi()->withData([
            'type'=> 'entries',
            'attributes' => $entry,
            'relationships' =>[
                'categories' =>[
                    'data' => [
                        'id'=> $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' =>[
                    'data' => [
                        'id'=> $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))->assertCreated();

        $this->assertDatabaseHas('entries',[
            'user_id' => $user->id,
            'name' => $entry['name'],
            'slug' => $entry['slug'],
            'username' => $entry['username'],
            'password' => $entry['password'],
            'url' => $entry['url'],
            'comment' => $entry['comment'],
        ]);
    }
}
