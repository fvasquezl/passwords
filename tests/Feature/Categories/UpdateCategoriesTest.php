<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_user_cannot_update_categories()
    {
        $category = Category::factory()->create();
        $this->jsonApi()->patch(route('api.v1.categories.update',$category))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function auth_user_can_update_their_categories()
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'id'=> $category->getRouteKey(),
            'attributes' =>[
                'name' => 'Name changed',
                'slug' => 'name-changed'
            ]
        ])->patch(route('api.v1.categories.update',$category))
            ->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('categories',[
            'name' => 'Name changed',
            'slug' => 'name-changed'
        ]);
    }

    /** @test */
    public function can_update_only_the_name()
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'id'=> $category->getRouteKey(),
            'attributes' =>[
                'name' => 'Name changed',
            ]
        ])->patch(route('api.v1.categories.update',$category))
            ->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('categories',[
            'name' => 'Name changed',
        ]);
    }

    /** @test */
    public function can_update_only_the_slug()
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'categories',
            'id'=> $category->getRouteKey(),
            'attributes' =>[
                'slug' => 'name-changed'
            ]
        ])->patch(route('api.v1.categories.update',$category))
            ->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('categories',[
            'slug' => 'name-changed'
        ]);
    }

}
