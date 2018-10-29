<?php

namespace BabyCheevies;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Password;

class UserActivation extends Model
{
    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_activations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['created_at','token'];

    /**
     * Get the tree the person belongs to.
     */
    public function user() {
        return $this->belongsTo('BabyCheevies\User');
    }
    
    public function save(array $options = []) {
        $this->created_at = $this->freshTimestamp();
        $this->token = Password::getRepository()->createNewToken();
        parent::save($options);
    }
}
