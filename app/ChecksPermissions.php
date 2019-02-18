<?php
namespace BabyCheevies;
use Illuminate\Support\Facades\Auth;
trait ChecksPermissions
{
    private function can($role)
    {
        if( !Auth::user()->can($role) ) {
            abort(403, 'Unauthorized action.');
        }        
    }
    
    private function isOrCan($id, $role)
    {
        if(!(Auth::user()->id == $id || Auth::user()->can($role))) {
            abort(403, 'Unauthorized action.');
        }
    }
    
    private function isUser($id)
    {
        if(Auth::user()->id != $id) {
            abort(403, 'Unauthorized action.');
        }
    }
}