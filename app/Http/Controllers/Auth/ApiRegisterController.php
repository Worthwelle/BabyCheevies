<?php

namespace BabyCheevies\Http\Controllers\Auth;

use BabyCheevies\UserActivation;
use BabyCheevies\User;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;

class ApiRegisterController extends RegisterController
{
    /**
     * Handle a registration request for the application.
     *
     * @override
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $errors = $this->validator($request->all())->errors();

        if(count($errors))
        {
            return response(['errors' => $errors], 401);
        }

        event(new Registered($user = $this->create($request->all())));
        
        $user->create_activation();

        //$this->guard()->login($user);

        return response(['user' => $user]);
    }
    
    /**
     * Handle an activation request for the application.
     *
     * @override
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function activate($token)
    {
        $activation = UserActivation::where('token',$token)->firstOrFail();
        $user = User::where('email',$activation->email)->first();
        $user->activate();

        $this->guard()->login($user);

        return response(['user' => $user]);
    }
}
