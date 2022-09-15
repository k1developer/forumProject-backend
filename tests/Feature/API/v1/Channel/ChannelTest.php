<?php

namespace Tests\Feature\API\v1\Channel;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    use RefreshDatabase;

    public function registerRolesAndPermissions()
    {
        $roleInDatabase = Role::where('name', config('permission.default_roles')[0]);
        if ($roleInDatabase->count() < 1) {
            foreach (config('permission.default_roles') as $role) {
                Role::create([
                    'name' => $role
                ]);
            }
        }
        $permissionInDatabase = Permission::where('name', config('permission.default_permissions')[0]);
        if ($permissionInDatabase->count() < 1) {
            foreach (config('permission.default_permissions') as $permission) {
                Permission::create([
                    'name' => $permission
                ]);
            }
        }
    }

    /**
     * Test All Channels List Should Be Accessible
     */
    public function test_all_channels_list_should_be_accessible()
    {
        $response = $this->get(route('channels.index'));

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test Create Channel
     */
    public function test_create_channel_should_be_validated()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');

        $response = $this->postJson(route('channels.store'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_create_new_channel()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');

        $response = $this->postJson(route('channels.store'), [
            'name' => 'laravel'
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertTrue(Channel::where('name', 'laravel')->exists());
    }

    /**
     * Test Update Channel
     */
    public function test_channel_update_should_be_validated()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');

        $channel = Channel::factory()->create();

        $response = $this->Json('PUT', route('channels.update', [$channel], []));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_channel_update()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');

        $channel = Channel::factory()->create([
            'name' => 'laravel'
        ]);
        $response = $this->Json('PUT', route('channels.update', [$channel]), [
            'name' => 'react js'
        ]);

        $updatedChannel = Channel::find($channel->id);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals('react js', $updatedChannel->name);
    }

    /**
     * Test Delete Channel
     */

    public function test_delete_channel()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');

        $channel = Channel::factory()->create();
        $response = $this->Json('DELETE', route('channels.destroy', [$channel]));

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(Channel::where('id', $channel->id)->count() === 0);
    }
}
