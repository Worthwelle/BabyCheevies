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
        $this->post('/api/v1/auth/register', [
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
     * @return void
     */
    public function testActivateUser()
    {
        $user = User::create([
            'name' => 'Mommy Dearest',
            'email' => 'mommy@dearest.com',
            'password' => bcrypt('mommydearestpass')
        ]);
        $activation = $user->create_activation();
        $this->get('/api/v1/auth/activate/'.$activation->token, ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
             ->assertJson([
                'user' => [
                    'name' => 'Mommy Dearest',
                    'email' => 'mommy@dearest.com'
                ]
             ]);
        $this->assertDatabaseHas('users', [
            'name' => 'Mommy Dearest',
            'email' => 'mommy@dearest.com',
            'activated' => true
        ]);
        $this->assertDatabaseMissing('user_activations', [
            'email' => 'mommy@dearest.com'
        ]);
        $this->assertTrue(Auth::user()->id == $user->id);
    }
}
