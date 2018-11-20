<?php

namespace Tests\API\Unit;

use Tests\TestCase;
use BabyCheevies\User;
use BabyCheevies\UserActivation;
use Illuminate\Support\Facades\Auth;

class UserControllerTest extends TestCase
{
    /*
     * Things to test:
     *  * Register user
     *  * Register user with existing email
     *  * List users
     *  * Login user
     *  * Login another user while a user is logged in
     *  * Log out user
     *  * Login another user
     *  * Remove a user
     * 
     * Once groups/roles are functional, tests will need to be updated.
     */
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testVersion()
    {
        $this->get('/api/v1/version', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
            'app' => "Baby Cheevies",
            'version' => '0.0'
             ]);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @depends testVersion
     * @return void
     */
    public function testRegisterUser()
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
     * @depends testRegisterUser
     * @return void
     */
    public function testActivateUser()
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
    public function testInvalidLoginUser()
    {
        User::create([
            'name' => 'Another User',
            'email' => 'another@test.com',
            'password' => bcrypt('testpassword')
        ])->activate();
        
        $this->post('/api/v1/login', [
            'email' => 'another@test.com',
            'password' => 'badpassword',
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJson([
                'message' => 'These credentials do not match our records.',
        ]);
        
        $this->get('/api/v1/test', ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson(['error' => 'Unauthenticated.']);
    }
    
    /**
     * Insert a user item without a custom slug.
     *
     * @return void
     */
    public function testValidLoginUser()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('testpassword')
        ])->activate();
        $this->post('/api/v1/login', [
            'email' => 'test@test.com',
            'password' => 'testpassword',
        ], ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->assertJsonStructure([
                'token'
        ]);
        
        $this->json('GET', '/api/v1/test')
             ->assertJson(["authenticated"]);
    }   
}
