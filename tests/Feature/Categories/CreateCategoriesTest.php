<?php

namespace Tests\Feature\Categories;

use App\Models\Category;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  Create Entries
     *
     * 'guest user'
     *  --- Cannot create an category
     * 'auth user'
     *  --- Can create an category
     * 'auth user'
     *  --- Cannot create an category without permissions
     * 'auth user'
     *  --- Cannot create an category on behalf another user
     *
     **/

    /** @test */
    public function guest_user_cannot_create_an_category()
    {
        $category = Category::factory()->raw();

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED); //401
    }

    /** @test */
    public function auth_users_can_create_an_category()
    {
        $category = Category::factory()->raw();
        $this->assertDatabaseMissing('categories', $category);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])->post(route('api.v1.categories.create'))
            ->assertCreated();

        $this->assertDatabaseHas('categories', [
            'name' => $category['name'],
            'slug' => $category['slug'],
        ]);
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
        $category = Category::factory()->raw([
            'name' => ''
        ]);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/name');

        $this->assertDatabaseMissing('categories', $category);
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
        $category = Category::factory()->raw([
            'slug' => null
        ]);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_must_be_unique()
    {
        Category::factory()->create(['slug' => 'same-slug']);
        $category = Category::factory()->raw(['slug' => 'same-slug']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_must_only_contains_letters_numbers_and_dashes()
    {
        $category = Category::factory()->raw([
            'slug' => '&&$^'
        ]);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_must_not_contains_underscores()
    {
        $category = Category::factory()->raw([
            'slug' => 'with_underscores'
        ]);

        Sanctum::actingAs($user = User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $category = Category::factory()->raw([
            'slug' => '-start-with-dashes'
        ]);

        Sanctum::actingAs($user = User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        $category = Category::factory()->raw([
            'slug' => 'end-with-dashes-'
        ]);

        Sanctum::actingAs($user = User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])->post(route('api.v1.categories.create'))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY) //422
            ->assertSee('data\/attributes\/slug');

        $this->assertDatabaseMissing('categories', $category);
    }

}
