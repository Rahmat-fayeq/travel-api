<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_cannot_access_adding_tour(): void
    {
        $travel = Travel::factory()->create();
        $response = $this->postJson('/api/v1/admin/travels/'.$travel->id.'/tours');

        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_adding_tour(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'editor')->value('id'));
        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/'.$travel->id.'/tours');

        $response->assertStatus(403);
    }

    public function test_saves_tour_succcessfully_with_valid_role_and_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $travel = Travel::factory()->create();
        $response = $this->actingAs($user)->postJson(
            '/api/v1/admin/travels/'.$travel->id.'/tours',
            ['name' => 'new tour']
        );
        $response->assertStatus(422);

        $travel = Travel::factory()->create();
        $response = $this->actingAs($user)->postJson(
            '/api/v1/admin/travels/'.$travel->id.'/tours',
            [
                'name' => 'new tour',
                'starting_date' => now()->toDateString(),
                'ending_date' => now()->addDay()->toDateString(),
                'price' => 123.45,
            ]
        );
        $response->assertStatus(201);

        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'new tour']);
    }
}
