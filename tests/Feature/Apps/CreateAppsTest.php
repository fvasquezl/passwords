<?php

namespace Tests\Feature\Apps;

use App\Models\App;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAppsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_single_app()
    {
        $app = App::factory()->create();

        $response = $this->getJson('/api/v1/apps'.$app->getRouteKey());

        $response->assertSee($app->title);

    }
}
