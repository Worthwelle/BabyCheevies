<?php

namespace BabyCheevies;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

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
}
