<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\ResetPasswordRequest;
use App\Http\Requests\Api\user\StoreUserRequest;
use App\Http\Requests\Api\user\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ResponseJsonTrait;
use App\Models\User;
use App\Notifications\UserRegisterionNotification;
use App\Notifications\WelcomeMessageNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ResponseJsonTrait;

    /**
     * Display a listing of the resource
     * @group Users
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('users.view-any');

        $users = User::orderBy('id', 'desc')->get();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage
     * @group Users
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        Gate::authorize('users.create');

        $user = User::create([
            "name" => $request->post('name'),
            "email" => $request->post('email'),
            "password" => Hash::make("password"),
            "type" => $request->post('type'),
        ]);

        if ($user) {
            $user->Profile()->create();

            $user->roles()->sync($request->post('roles'));

            $user->notify(new WelcomeMessageNotifications($user));

            return new UserResource($user);

        } else {
            return $this->responseJson(false, 'Something Went Wrong', null, 500);
        }
    }

    /**
     * Display the specified resource
     * @group Users
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        Gate::authorize('users.view');

        $user = User::find($id);

        if ($user)
        {
            return new UserResource($user);
        } else {
            return $this->responseJson(false, 'Sorry, user with id ' . $id . ' cannot be found', null, 404);
        }
    }

    /**
     * Update the specified resource in storage
     * @group Users
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        Gate::authorize('users.update');

        $user = User::find($id);

        if ($user) {
            $status = $user->update($request->all());

            if ($status) {
                $user->roles()->sync($request->post('roles'));

                return $this->responseJson(true, 'the user updated successfully');
            }
        } else {
            return $this->responseJson(false, 'Something Went Wrong');
        }
    }

    /**
     * Remove the specified resource from storage
     * @group Users
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Gate::authorize('users.delete');

        $user = User::find($id);

        Storage::disk('uploads')->delete($user->profile->getRawOriginal('photo'));

        if ($user->delete())
        {
            return $this->responseJson(true, 'the user deleted successfully');
        } else {
            return $this->responseJson(false, 'the user could not be deleted');
        }
    }

    /**
     * Approve To Be Blogger
     * @group Users
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function AproveBlogger($id)
    {
        $user = User::find($id);
        $auth_user = auth('api')->user();

        if ($user and $auth_user->type == 'admin') {
            $status = $user->update(['type' => 'blogger' ]);

            if ($status) {
                Notification::send($user, new UserRegisterionNotification($user));
                return $this->responseJson(true, 'You Aproved the user successfuly', null, 200);
            } else {
                return $this->responseJson(false, 'Something Went Wrong', null);
            }
        } else {
            return $this->responseJson(false, 'Something Went Wrong', null);
        }
    }

    /**
     * Reset Password
     * @group Users
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = auth('api')->user();

        $status = $user->update([
            'password' => Hash::make($request->password),
        ]);

        if ($status)
        {
            return $this->responseJson(true, 'the password updated successffuly', null, 200);
        } else {
            return $this->responseJson(false, 'Something Went Wrong', null, 500);
        }
    }
}
