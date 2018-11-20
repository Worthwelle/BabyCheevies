<?php

namespace BabyCheevies\Http\Controllers\Auth;

use BabyCheevies\User;
use BabyCheevies\UserActivation;
use BabyCheevies\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends RegisterController
{

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }
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

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
