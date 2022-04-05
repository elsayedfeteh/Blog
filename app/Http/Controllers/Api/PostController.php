<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Post\StorePostRequest;
use App\Http\Requests\Api\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Traits\ResponseJsonTrait;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Notifications\PostChangeStatusNotification;
use App\Notifications\PostCreateNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use ResponseJsonTrait;

    /**
     * Get All Post.
     *
     * @group Posts
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth('api')->user();

        if ($user->type == 'admin') {
            $posts = Post::with('user')->orderBy('id', 'desc')->paginate(5);
        } else {
            $posts = $user->posts()->orderBy('id', 'desc')->paginate(5);
        }

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage
     * @group Posts
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            $image_path = $image->store('posts', 'uploads');

            $request->merge(['image_path' => $image_path]);
        }

        $post = Post::create([
            "image_path" => $request->image_path,
            "title" => $request->post('title'),
            "summary" => $request->post('summary'),
            "body" => $request->post('body'),
            "user_id" => auth('api')->user()->id,
            "status" => auth('api')->user()->type == 'admin' ? 'active':'inactive',
        ]);

        $tags = explode(',', $request->tags);

        foreach ($tags as $tag) {
            $tag = Tag::firstOrCreate(['name' => $tag]);
            $post->tags()->syncWithoutDetaching($tag->id);
        }

        $categories = explode(',', $request->categories);

        foreach ($categories as $category) {
            $category = Category::firstOrCreate(['name' => $category]);
            $post->categories()->syncWithoutDetaching($category->id);
        }

        if ($post->status == 'inactive') {
            $users = User::where('type','admin')->get();

            Notification::send($users, new PostCreateNotification($post));
        }

        return new PostResource($post);
    }

    /**
     * Display the specified post
     *
     * @group Posts
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        if ($post) {
            return new PostResource($post);
        } else {
            return $this->responseJson(false, 'not found', null, 404);
        }
    }

    /**
     * Update the specified resource in storage
     * @group Posts
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = auth('api')->user()->posts()->find($id);

        if (! $post)
        {
            return $this->responseJson(false, 'Sorry, post with id ' . $id . ' cannot be found', null, 404);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            Storage::disk('uploads')->delete($post->image_path);

            $image_path = $image->store('posts', 'uploads');

            $request->merge(['image_path' => $image_path]);
        }

        $post->update($request->all());

        $tags = explode(',', $request->tags);

        foreach ($tags as $tag) {
            $tag = Tag::firstOrCreate(['name' => $tag]);
            $post->tags()->syncWithoutDetaching($tag->id);
        }

        $categories = explode(',', $request->categories);

        foreach ($categories as $category) {
            $category = Category::firstOrCreate(['name' => $category]);
            $post->categories()->syncWithoutDetaching($category->id);
        }

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage
     * @group Posts
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = auth('api')->user()->posts()->find($id);

        if (! $post)
        {
            return $this->responseJson(false, 'Sorry, post with id ' . $id . ' cannot be found', null, 404);
        }

        if ($post->delete())
        {
            return $this->responseJson(true, 'the post deleted successfully', null, 200);
        } else {
            return $this->responseJson(false, 'the post could not be deleted', null);
        }

    }

    /**
     * Approve to publish Post
     *
     * @group Posts
     *
     * @bodyParam id required ID of Posts
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(Request $request)
    {
        Gate::authorize('post.changeStatus');

        $post = Post::find($request->id);

        if ($post and $post->status == "inactive") {
            $status = $post->update(['status' => 'active' ]);

            if ($status) {

                Notification::send($post->user, new PostChangeStatusNotification($post));

                return $this->responseJson(true, 'the status updated successffuly', null, 200);
            } else {
                return $this->responseJson(false, 'Something Went Wrong', null);
            }
        } else {
            return $this->responseJson(false, 'Something Went Wrong', null);
        }
    }
}
