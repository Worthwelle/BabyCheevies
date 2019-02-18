<?php

namespace BabyCheevies\Http\Controllers\Auth;

use BabyCheevies\ChecksPermissions;
use BabyCheevies\User;
use BabyCheevies\UserActivation;
use BabyCheevies\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, ChecksPermissions;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['destroy', 'update']]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data, $optional = false)
    {
        if( $optional ) {
            return Validator::make($data, [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users',
                'password' => 'nullable|string|min:6|confirmed',
            ]);
        }
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \BabyCheevies\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
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

        event(new Registered($user = $this->create($request->only('name', 'email', 'password', 'password_confirmation'))));
        
        $user->create_activation();

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
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->isOrCan($id, 'delete_users');
        $user = User::find($id);
        
        $activation = UserActivation::where('email',$user->email);
        if( $activation !== null ) {
            $activation->delete();
        }
        $user->delete();

        return response()->json(['message' => 'successful']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->isOrCan($id, 'edit_users');
        
        $errors = $this->validator($input = $request->only('name', 'email', 'password', 'password_confirmation'), true)->errors();
        if(count($errors))
        {
            return response(['errors' => $errors], 401);
        }
        
        $user = User::where('id',$id)->first();
        
        if( isset( $request['name'] ) ) {
            $user->name = $request['name'];
        }
        if( isset( $request['email'] ) ) {
            $user->email = $request['email'];
        }
        if( isset( $request['password'] ) ) {
            $user->password = $request['password'];
        }
        
        $user->save();

        return response()->json(['user' => $user]);
    }
}
