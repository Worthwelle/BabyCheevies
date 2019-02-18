<?php

namespace BabyCheevies\Http\Controllers;

use BabyCheevies\ChecksPermissions;
use BabyCheevies\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ChecksPermissions;

    /**
     * Display a listing of the resource.
     *
     * @group changed
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->can('view_roles');
        return response(Role::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->can('create_roles');
        return Role::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->can('view_roles');
        return Role::find($id);
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
        $this->can('edit_roles');
        $role = Role::find($id);
        $role->name = $request['name'];
        $role->label = $request['label'];
        $role->save();
        return $role;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->can('delete_roles');
        $role = Role::find($id);
        $role->delete();
        return response()->json(['message' => 'successful']);
    }
}
