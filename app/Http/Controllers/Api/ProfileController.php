<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Traits\ResponseJsonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use ResponseJsonTrait;
    /**
     * Display a listing of the resource
     * @group Profile
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $profile = auth('api')->user()->profile;

        if ($profile)
        {
            return new ProfileResource($profile);
        } else {
            return $this->responseJson(false, 'Something Went Wrong', null);
        }
    }

    /**
     * Update the specified resource in storage
     * @group Profile
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProfileRequest $request)
    {
        $profile = auth('api')->user()->profile;

        if (! $profile)
        {
            return $this->responseJson(false, 'Something Went Wrong', null);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            Storage::disk('uploads')->delete($profile->getRawOriginal('photo'));

            $photo_path = $file->store('avatare', 'uploads');

            $request->merge([
                'photo' => $photo_path,
            ]);
        }

        $profile->update($request->all());

        $profile->user->update($request->all());

        return $this->responseJson(true, 'the profile updated successfuly', null);
    }
}
