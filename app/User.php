<?php

namespace BabyCheevies;

use BabyCheevies\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;

class User extends Authenticatable implements AuthenticatableUserContract
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'activated',
    ];
    
    public function create_activation() {
        return UserActivation::create([
            'email' => $this->email
        ]);
    }
    
    public function activate() {
        if( $this->activated ) {
            return;
        }
        $activation = UserActivation::where('email',$this->email);
        if( $activation !== null ) {
            $activation->delete();
        }
        $this->activated = true;
        $this->save();
    }
    
    public function active() {
        return $this->activated;
    }

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();  // Eloquent model method
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
             'user' => [ 
                'id' => $this->id,
                'email' => $this->email
             ]
        ];
    }
}
