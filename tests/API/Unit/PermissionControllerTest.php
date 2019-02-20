<?php

namespace Tests\Unit\API;

use Tests\TestCase;
use BabyCheevies\Permission;
use BabyCheevies\User;
use Illuminate\Support\Facades\Auth;

class PermissionControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNonadminCannotListPermissions()
    {
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        $this->get('/api/v1/permissions', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(200);
        $this->get('/api/v1/permissions', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminCanListPermissions()
    {
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $permission = factory(Permission::class)->create();
        
        $this->get('/api/v1/permissions', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJsonFragment([
                    'name' => $permission->name,
                    'label' => $permission->label,
             ]);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNonadminCannotCreatePermissions()
    {
        $faker = \Faker\Factory::create();
        $name = $faker->word;
        $label = $faker->words(3, true);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        $this->post('/api/v1/permissions', ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(200);
        $this->post('/api/v1/permissions', ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminCanCreatePermissions()
    {
        $faker = \Faker\Factory::create();
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $name = $faker->word;
        $label = $faker->words(3, true);
        
        $this->post('/api/v1/permissions', ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
                    'name' => $name,
                    'label' => $label,
             ]);
        $this->assertDatabaseHas('permissions', [
            'name' => $name,
            'label' => $label,
        ]);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNonadminCannotEditPermissions()
    {
        $faker = \Faker\Factory::create();
        $role = factory(Permission::class)->create();
        $name = $faker->word;
        $label = $faker->words(3, true);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        $this->put('/api/v1/permissions/'.$role->id, ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(200);
        $this->put('/api/v1/permissions/'.$role->id, ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminCanEditPermissions()
    {
        $faker = \Faker\Factory::create();
        $role = factory(Permission::class)->create();
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $name = $faker->word;
        $label = $faker->words(3, true);
        
        $this->put('/api/v1/permissions/'.$role->id, ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
                    'name' => $name,
                    'label' => $label,
             ]);
        $this->assertDatabaseHas('permissions', [
            'name' => $name,
            'label' => $label,
        ]);
    }
    
    /**
     * A basic test example.
     *
     * @group changed
     * @return void
     */
    public function testNonadminCannotDeletePermissions()
    {
        $this->markTestSkipped('Guest user is redirected to login page when attempting to delete.');
        $role = factory(Permission::class)->create();
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        $this->delete('/api/v1/permissions/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(200);
        $this->delete('/api/v1/permissions/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminCanDeletePermissions()
    {
        $role = factory(Permission::class)->create();
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $this->assertDatabaseHas('permissions', [
            'name' => $role->name,
            'label' => $role->label,
        ]);
        $this->delete('/api/v1/permissions/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'message' => 'successful'
        ]);
        $this->assertDatabaseMissing('roles', [
            'name' => $role->name,
            'label' => $role->label,
        ]);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNonadminCannotViewARole()
    {
        $role = factory(Permission::class)->create();
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        $this->get('/api/v1/permissions/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(200);
        $this->get('/api/v1/permissions/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminCanViewARole()
    {
        $role = factory(Permission::class)->create();
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $this->get('/api/v1/permissions/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
            'name' => $role->name,
            'label' => $role->label,
        ]);
    }
}
