<?php

namespace Tests\Feature\Entries;

use App\Models\Category;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateEntriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  Create Entries
     *
     * 'guest user'
     *  --- Cannot create an entry
     * 'auth user'
     *  --- Can create an entry
     * 'auth user'
     *  --- Cannot create an entry without permissions
     * 'auth user'
     *  --- Cannot create an entry on behalf another user
     *
     **/

    /** @test */
    public function guest_user_cannot_create_an_entry()
    {
        $entry = array_filter(Entry::factory()->raw([
            'user_id' => null
        ]));

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED); //401
    }

    /** @test */
    public function auth_users_can_create_an_entry()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $entry = array_filter(Entry::factory()->raw([
            'category_id' => null,
            'user_id' => null
        ]));


        $this->assertDatabaseMissing('entries', $entry);

        Sanctum::actingAs($user, ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))->assertCreated();

        $this->assertDatabaseHas('entries', [
            'user_id' => $user->id,
            'name' => $entry['name'],
            'slug' => $entry['slug'],
            'username' => $entry['username'],
            'password' => $entry['password'],
            'url' => $entry['url'],
            'comment' => $entry['comment'],
        ]);
    }

    /** @test */
    public function auth_users_cannot_create_an_entry_without_permissions()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $entry = array_filter(Entry::factory()->raw([
            'category_id' => null,
            'user_id' => null
        ]));

        $this->assertDatabaseMissing('entries', $entry);

        Sanctum::actingAs($user);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatusCode(Response::HTTP_FORBIDDEN); //403

        $this->assertDatabaseCount('entries', 0);
    }

    /** @test */
    public function auth_users_cannot_create_an_entry_on_behalf_another_user()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $entry = array_filter(Entry::factory()->raw([
            'category_id' => null,
            'user_id' => null
        ]));

        $this->assertDatabaseMissing('entries', $entry);

        Sanctum::actingAs($user, ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => User::factory()->create()->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatusCode(Response::HTTP_FORBIDDEN); //403

        $this->assertDatabaseCount('entries', 0);
    }


    /**
     *  Testing sent data fields
     *
     * 'Authors'
     *  --- Is required
     *  --- Must be an relationship object
     *
     **/

    /** @test */
    public function authors_is_required()
    {
        $entry = Entry::factory()->raw();
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ]
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertJsonFragment(['source' => ['pointer' => '/data']]);

        $this->assertDatabaseMissing('entries', $entry);
    }

    /** @test */
    public function authors_must_be_a_relationship_object()
    {
        $entry = Entry::factory()->raw();
        $entry['authors'] = 'other';

        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ]
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)  //422
            ->assertSee('data\/attributes\/authors');

        $this->assertDatabaseMissing('entries', $entry);
    }


    /**
     * 'Categories'
     *  --- Is required
     *  --- Must be an relationship object
     **/

    /** @test */
    public function categories_is_required()
    {
        $user = User::factory()->create();

        $entry = array_filter(Entry::factory()->raw([
            'category_id' => null,
            'user_id' => null
        ]));

        Sanctum::actingAs($user, ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)//422
            ->assertJsonFragment(['source' => ['pointer' => '/data']]);

        $this->assertDatabaseMissing('entries', $entry);
    }

    /** @test */
    public function categories_must_be_a_relationship_object()
    {
        $entry = Entry::factory()->raw([
            'category_id' => null
        ]);
        $entry['categories'] = 'slug';

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)  //422
            ->assertSee('data\/attributes\/categories');

        $this->assertDatabaseMissing('entries', $entry);
    }


    /**
     *
     * 'name'
     * --- Is required
     *
     **/


    /** @test */
    public function name_is_required()
    {
        $entry = Entry::factory()->raw([
            'name' => null
        ]);
        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/name');

        $this->assertDatabaseMissing('entries', $entry);
    }





    /**
     *
     * 'slug'
     * --- Is required
     * --- Must be Unique
     * --- Must only contains letters, numbers and dashes
     * --- Must not contains underscores("/_/")
     * --- Must not start whit dashes ("/^-/")
     * --- Must not end whit dashes ("/-$/")
     *
     **/


    /** @test */
    public function slug_is_required()
    {
        $entry = Entry::factory()->raw([
            'slug' => null
        ]);
        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('entries', $entry);
    }

    /** @test */
    public function slug_must_be_unique()
    {
        Entry::factory()->create([
            'slug' => 'slug-same'
        ]);

        $entry = Entry::factory()->raw([
            'slug' => 'slug-same'
        ]);

        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('entries', $entry);
    }

    /** @test */
    public function slug_must_only_contains_letters_numbers_and_dashes()
    {
        $entry = Entry::factory()->raw([
            'slug' => '&&$^'
        ]);

        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('entries', $entry);
    }

    /** @test */
    public function slug_must_not_contains_underscores()
    {
        $entry = Entry::factory()->raw([
            'slug' => 'with_underscores'
        ]);

        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('entries', $entry);
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $entry = Entry::factory()->raw([
            'slug' => '-start-with-dashes'
        ]);

        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('entries', $entry);
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        $entry = Entry::factory()->raw([
            'slug' => 'end-with-dashes-'
        ]);

        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('entries', $entry);
    }


    /**
     *
     * 'username'
     * --- Is required
     *
     **/

    /** @test */
    public function username_is_required()
    {
        $entry = Entry::factory()->raw([
            'username' => null
        ]);
        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/username');

        $this->assertDatabaseMissing('entries', $entry);
    }

    /**
     *
     * 'password'
     * --- Is required
     *
     **/

    /** @test */
    public function password_is_required()
    {
        $entry = Entry::factory()->raw([
            'password' => null
        ]);
        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/password');

        $this->assertDatabaseMissing('entries', $entry);
    }

    /**
     *
     * 'url'
     * --- Is required
     *
     **/

    /** @test */
    public function url_is_required()
    {
        $entry = Entry::factory()->raw([
            'url' => null
        ]);
        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/url');

        $this->assertDatabaseMissing('entries', $entry);
    }

    /** @test */
    public function url_must_have_url_format()
    {
        $entry = Entry::factory()->raw([
            'url' => 'url sin format'
        ]);
        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/url');

        $this->assertDatabaseMissing('entries', $entry);
    }


    /**
     *
     * 'comment'
     * --- Is required
     *
     **/
    /** @test */
    public function comment_is_required()
    {
        $entry = Entry::factory()->raw([
            'comment' => null
        ]);
        $category = Category::factory()->create();

        Sanctum::actingAs($user = User::factory()->create(), ['entries:create']);

        $this->jsonApi()->withData([
            'type' => 'entries',
            'attributes' => $entry,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ],
                'authors' => [
                    'data' => [
                        'id' => $user->getRouteKey(),
                        'type' => 'authors'
                    ]
                ],
            ]
        ])->post(route('api.v1.entries.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/comment');

        $this->assertDatabaseMissing('entries', $entry);
    }

}
