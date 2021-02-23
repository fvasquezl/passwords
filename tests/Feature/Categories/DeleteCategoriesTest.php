<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteCategoriesTest extends TestCase
{
    use RefreshDatabase;

    use RefreshDatabase;
    /*
     * Guest Users
     * --- cannot delete categories
     * Auth Users
     * --- can_delete_their_categories
     * --- can_delete_their_categories
     * --- cannot_delete_their_categories_without_permissions
     * --- cannot_delete_other_categories
     */

    /** @test */
    public function guest_users_cannot_delete_categories()
    {
        $category = Category::factory()->create();
        $this->jsonApi()
            ->delete(route('api.v1.categories.delete',$category))
            ->assertStatus(Response::HTTP_UNAUTHORIZED); //401
    }

    /** @test */
    public function auth_users_can_delete_their_categories()
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->delete(route('api.v1.categories.delete',$category))
            ->assertStatus(Response::HTTP_NO_CONTENT); //204
    }

}
