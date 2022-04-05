<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Role\StoreRoleRequest;
use App\Http\Requests\Api\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Traits\ResponseJsonTrait;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    use ResponseJsonTrait;
    /**
     * Display a listing of the resource
     * @group Roles
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('roles.view-any');

        $roles = Role::orderBy('id', 'desc')->get();

        if ($roles)
        {
            return RoleResource::collection($roles);

        } else {
            return $this->responseJson(false, 'Something Went Wrong', null);
        }
    }

    public function permissions()
    {
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => config('permissions')
        ]);
    }

    /**
     * Store a newly created resource in storage
     * @group Roles
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request)
    {
        Gate::authorize('roles.create');

        $role = Role::create([
            'name' => $request->post('name'),
            'permissions' => $request->post('permissions'),
        ]);

        if ($role) {
            return new RoleResource($role);
        } else {
            return $this->responseJson(false, 'Something Went Wrong', null);
        }
    }

    /**
     * Display the specified resource
     * @group Roles
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Gate::authorize('')

        $role = Role::find($id);

        if ($role)
        {
            return new RoleResource($role);

        } else {
            return $this->responseJson(false, 'Sorry, role with id ' . $id . ' cannot be found', null, 404);
        }
    }

    /**
     * Update the specified resource in storage
     * @group Roles
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        Gate::authorize('roles.update');

        $role = Role::find($id);

        if (! $role)
        {
            return $this->responseJson(false, 'Sorry, role with id ' . $id . ' cannot be found', null, 404);
        }

        $status = $role->update($request->all());

        if ($status) {
            return (new RoleResource($role))->additional(['message' => 'the role update sucessfully']);
        } else {
            return $this->responseJson(true, 'the role could not be updated');
        }
    }

    /**
     * Remove the specified resource from storage
     * @group Roles
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Gate::authorize('roles.delete');

        $role = Role::find($id);

        if (! $role)
        {
            return $this->responseJson(false, 'Sorry, role with id ' . $id . ' cannot be found', null, 404);
        }

        if ($role->delete())
        {
            return $this->responseJson(true, 'the role deleted successfully', null);
        } else {
            return $this->responseJson(false, 'the role could not be deleted', null);
        }
    }
}
