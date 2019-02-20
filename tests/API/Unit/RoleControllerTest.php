<?php

namespace Tests\Unit\API;

use Tests\TestCase;
use BabyCheevies\Role;
use BabyCheevies\User;
use Illuminate\Support\Facades\Auth;

class RoleControllerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNonadminCannotListRoles()
    {
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        $this->get('/api/v1/roles', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(200);
        $this->get('/api/v1/roles', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminCanListRoles()
    {
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $role = factory(Role::class)->create();
        
        $this->get('/api/v1/roles', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJsonFragment([
                    'name' => $role->name,
                    'label' => $role->label,
             ]);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNonadminCannotCreateRoles()
    {
        $faker = \Faker\Factory::create();
        $name = $faker->word;
        $label = $faker->words(3, true);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        $this->post('/api/v1/roles', ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(200);
        $this->post('/api/v1/roles', ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminCanCreateRoles()
    {
        $faker = \Faker\Factory::create();
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $name = $faker->word;
        $label = $faker->words(3, true);
        
        $this->post('/api/v1/roles', ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
                    'name' => $name,
                    'label' => $label,
             ]);
        $this->assertDatabaseHas('roles', [
            'name' => $name,
            'label' => $label,
        ]);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNonadminCannotEditRoles()
    {
        $faker = \Faker\Factory::create();
        $role = factory(Role::class)->create();
        $name = $faker->word;
        $label = $faker->words(3, true);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        $this->put('/api/v1/roles/'.$role->id, ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(200);
        $this->put('/api/v1/roles/'.$role->id, ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminCanEditRoles()
    {
        $faker = \Faker\Factory::create();
        $role = factory(Role::class)->create();
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $name = $faker->word;
        $label = $faker->words(3, true);
        
        $this->put('/api/v1/roles/'.$role->id, ['name' => $name, 'label' => $label], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
                    'name' => $name,
                    'label' => $label,
             ]);
        $this->assertDatabaseHas('roles', [
            'name' => $name,
            'label' => $label,
        ]);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testNonadminCannotDeleteRoles()
    {
        $role = factory(Role::class)->create();
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        $this->delete('/api/v1/roles/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(200);
        $this->delete('/api/v1/roles/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminCanDeleteRoles()
    {
        $role = factory(Role::class)->create();
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $this->assertDatabaseHas('roles', [
            'name' => $role->name,
            'label' => $role->label,
        ]);
        $this->delete('/api/v1/roles/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
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
        $role = factory(Role::class)->create();
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        $this->get('/api/v1/roles/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
        
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(200);
        $this->get('/api/v1/roles/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(403);
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAdminCanViewARole()
    {
        $role = factory(Role::class)->create();
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $this->get('/api/v1/roles/'.$role->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
            'name' => $role->name,
            'label' => $role->label,
        ]);
    }
}
