<?php

namespace BabyCheevies\Http\Controllers\Auth;

use BabyCheevies\Http\Controllers\Controller;
use BabyCheevies\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $this->credentials($request);
        $username = $credentials[$this->username()];
        $user = User::where($this->username(),$username)->first();
        if( !$user->active() ) {
            return $this->notActivated($request);
        }
        
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }
        
        if ($token = $this->guard()->attempt($credentials)) {
            return $this->sendLoginResponse($request, $token);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request, $token)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user(), $token);
    }
    
    protected function authenticated(Request $request, $user, $token)
    {
        return response()->json([
            'token' => $token,
        ]);
    }
    
    protected function notActivated(Request $request)
    {
        return response()->json([
            'message' => trans('auth.activationpending'),
        ], 401);
    }
    
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return response()->json([
            'message' => trans('auth.failed'),
        ], 401);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return response()->json([
            'message' => trans('auth.failed'),
        ], 401);
    }
    
    public function logout()
    {
       $this->guard()->logout(); // pass true to blacklist forever
       return response()->json([
            'message' => trans('auth.logout'),
        ], 200);
    }
}
