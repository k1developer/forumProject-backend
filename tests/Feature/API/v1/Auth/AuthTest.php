<?php

namespace Tests\Feature\API\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function registerRolesAndPermission()
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
     * Test Register
     */
    public function test_register_should_be_validated()
    {
        $response = $this->postJson(route('auth.register'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_new_user_can_register()
    {
        $this->registerRolesAndPermission();
        $response = $this->postJson(route('auth.register'), [
            'name' => 'keyvan dsahtban',
            'email' => 'keyvan@gmail.com',
            'password' => '12345678'
        ]);


        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertTrue(User::where('email', 'keyvan@gmail.com')->count() > 0);
    }

    /**
     * Test Login
     */

    public function test_login_should_be_validated()
    {
        $response = $this->postJson(route('auth.login'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_user_can_login_with_true_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }


    /**
     * Test Logged In User
     */
    public function test_show_user_info_if_logged_in()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('auth.user'));

        $response->assertStatus(Response::HTTP_OK);
    }


    /**
     * Logout User
     */
    public function test_logged_in_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('auth.logout'));

        $response->assertStatus(Response::HTTP_OK);

        $this->assertNull(auth()->user());
    }
}
