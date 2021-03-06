<?php

namespace Tests\API\Unit;

use Tests\TestCase;
use BabyCheevies\User;
use Illuminate\Support\Facades\Auth;

class UserControllerTest extends TestCase
{
    /*
     * Things to test:
     *  * List users
     *  * Login another user while a user is logged in
     *  * Remove a user
     * 
     * Once groups/roles are functional, tests will need to be updated.
     */
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testCanRetrieveVersion()
    {
        $this->get('/api/v1/version', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
            'app' => "Baby Cheevies",
             ]);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testCanRegisterUsers()
    {
        $this->post('/api/v1/register', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'johndoepass',
            'password_confirmation' => 'johndoepass',
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
                'user' => [
                    'name' => 'John Doe',
                    'email' => 'john@doe.com'
                ]
             ]);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'activated' => false
        ]);
        $this->assertDatabaseHas('user_activations', [
            'email' => 'john@doe.com'
        ]);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @depends testCanRegisterUsers
     * @return void
     */
    public function testCannotRegisterUserWithExistingEmail()
    {
        $this->assertDatabaseHas('users', [
            'email' => 'john@doe.com',
        ]);
        $this->post('/api/v1/register', [
            'name' => 'John Doe Again',
            'email' => 'john@doe.com',
            'password' => 'johndoepass',
            'password_confirmation' => 'johndoepass',
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
                'errors' => [
                    'email' => ['The email has already been taken.'],
                ]
             ]);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @depends testCanRegisterUsers
     * @return void
     */
    public function testCanActivateUser()
    {
        $user = factory(User::class)->create();
        $activation = $user->create_activation();
        $this->get('/api/v1/activate/'.$activation->token, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ]
             ]);
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'activated' => true
        ]);
        $this->assertDatabaseMissing('user_activations', [
            'email' => $user->email,
        ]);
        $this->assertTrue(Auth::user()->id == $user->id);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testCanLoginValidUser()
    {
        $user = factory(User::class)->create();
        $user->activate();
        
        $this->post('/api/v1/login', [
            'email' => $user->email,
            'password' => 'secret',
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJsonStructure([
                'token'
        ]);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson(["user" => ['email' => $user->email]]);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testCanLogoutValidUser()
    {
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson(["user" => ['email' => $user->email]]);
        
        $this->get('/api/v1/logout', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
                 'message' => 'You have been logged out.'
             ]);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testCannotLoginInvalidUser()
    {
        $user = factory(User::class)->create();
        $user->activate();
        
        $this->post('/api/v1/login', [
            'email' => $user->email,
            'password' => "notsecret",
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'message' => 'These credentials do not match our records.',
        ]);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertStatus(401);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testCannotLoginInactiveUser()
    {
        $user = factory(User::class)->create();
        $user->create_activation();
        
        $this->post('/api/v1/login', [
            'email' => $user->email,
            'password' => 'secret',
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'message' => 'This account has not been activated. Please check your email.'
        ]);
        
        $this->get('/api/v1/me', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson(['error' => 'Unauthenticated.']);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testAdminCanEditUser()
    {
        $faker = \Faker\Factory::create();
        $user = factory(User::class)->create();
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        $newName = $faker->name;
        $newEmail = $faker->email;
        $newPass = bcrypt("newsecret");
        Auth::login($admin);
        
        $this->put('/api/v1/user/'.$user->id, ['name' => $newName], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'user' => [
                    'name' => $newName,
                    'email' => $user->email,
                ]
        ]);
        $this->put('/api/v1/user/'.$user->id, ['name' => $user->name, 'email' => $newEmail], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'user' => [
                    'name' => $user->name,
                    'email' => $newEmail,
                ]
        ]);
        $this->put('/api/v1/user/'.$user->id, ['password' => $newPass, 'password_confirmation' => $newPass], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'user' => [
                    'name' => $user->name,
                    'email' => $newEmail,
                ]
        ]);
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $newEmail,
        ]);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testCanSelfEditUser()
    {
        $faker = \Faker\Factory::create();
        $user = factory(User::class)->create();
        $user->activate();
        $newName = $faker->name;
        $newEmail = $faker->email;
        $newPass = bcrypt("newsecret");
        Auth::login($user);
        
        $this->put('/api/v1/user/'.$user->id, ['name' => $newName], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'user' => [
                    'name' => $newName,
                    'email' => $user->email,
                ]
        ]);
        $this->put('/api/v1/user/'.$user->id, ['name' => $user->name, 'email' => $newEmail], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'user' => [
                    'name' => $user->name,
                    'email' => $newEmail,
                ]
        ]);
        $this->put('/api/v1/user/'.$user->id, ['password' => $newPass, 'password_confirmation' => $newPass], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'user' => [
                    'name' => $user->name,
                    'email' => $newEmail,
                ]
        ]);
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $newEmail,
        ]);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testCannotEditOtherUser()
    {
        $faker = \Faker\Factory::create();
        $user = factory(User::class)->create();
        $user->activate();
        $other = factory(User::class)->create();
        $other->activate();
        $newName = $faker->name;
        Auth::login($other);
        
        $this->put('/api/v1/user/'.$user->id, ['name' => $newName], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertStatus(403);
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email
        ]);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testAdminCanDeleteUser()
    {
        $user = factory(User::class)->create();
        $admin = factory(User::class)->create();
        $admin->activate();
        $admin->assignRole('administrator');
        Auth::login($admin);
        
        $this->delete('/api/v1/user/'.$user->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'message' => 'successful'
        ]);
        $this->assertDatabaseMissing('users', [
            'name' => $user->name,
            'email' => $user->email,
            'activated' => true
        ]);
        $this->assertDatabaseMissing('user_activations', [
            'email' => $user->email,
        ]);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testCanSelfDeleteUser()
    {
        $user = factory(User::class)->create();
        $user->activate();
        Auth::login($user);
        
        $this->delete('/api/v1/user/'.$user->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'message' => 'successful'
        ]);
        $this->assertDatabaseMissing('users', [
            'name' => $user->name,
            'email' => $user->email,
            'activated' => true
        ]);
        $this->assertDatabaseMissing('user_activations', [
            'email' => $user->email,
        ]);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testCannotDeleteAnotherUser()
    {
        $user = factory(User::class)->create();
        $user->activate();
        $user2 = factory(User::class)->create();
        $user2->activate();
        Auth::login($user2);
        
        $this->delete('/api/v1/user/'.$user->id, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertStatus(403);
        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'activated' => true
        ]);
    }
}
