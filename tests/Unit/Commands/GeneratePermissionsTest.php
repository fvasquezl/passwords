<?php

namespace Tests\Unit\Commands;



use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneratePermissionsTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function can_generate_permissions_for_registered_api_resources()
    {
        config(['json-api-v1.resources'=>[
            'entries' => \App\Models\Entry::class
        ]]);

       $this->artisan('generate:permissions')
       ->expectsOutput('Permissions generated!')
       ;


        foreach (Permission::$abilities as $ability){
            $this->assertDatabaseHas('permissions',[
               'name' => "entries:{$ability}"
           ]);
        }

        $this->artisan('generate:permissions')
            ->expectsOutput('Permissions generated!')
        ;

        $this->assertDatabaseCount('permissions', count(Permission::$abilities));
    }
}
