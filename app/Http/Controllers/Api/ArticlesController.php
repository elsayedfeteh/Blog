<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Search\SearchRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\TagResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseJsonTrait;

class ArticlesController extends Controller
{
    use ResponseJsonTrait;

    /**
     * Get All Articles
     *
     * @group Articles
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function index()
    {
        $posts = Post::with('user', 'comments')->active()->paginate();

        return ArticleResource::collection($posts);
    }

    /**
     * Display the specified Article
     *
     * @group Articles
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function show($id)
    {
        $post = Post::with('comments')->active()->find($id);
        $auth_user = auth('api')->user();

        if (! $post)
        {
            return $this->responseJson(false, 'Sorry, article with id ' . $id . ' cannot be found', null,404);
        }

        if ($auth_user) {
            if ($auth_user->type != 'admin' and $auth_user != $post->user) {
                $post->increment('total_views');
            }
        } else {
            $post->increment('total_views');
        }

        return new ArticleResource($post);
    }

    /**
     * Search About Articles
     *
     * @group Articles
     *
     * @param  string  $postTitle
     * @param  string  $categoryName
     * @param  string  $tagName
     *
     * @return \Illuminate\Http\JsonResponse
    */

    public function search(SearchRequest $request)
    {
        $posts = null;

        if ($request->name != null) {
            $posts = Post::where('title', 'Like', '%' . $request->name . '%')
                ->orderBy('id', 'desc')->get();
        }

        if ($request->category != null) {
            $posts = Category::where('name', $request->category)->first()->posts()->orderBy('id', 'desc')->get();
        }

        if ($request->tag != null) {
            $posts = Tag::where('name', $request->tag)->first()->posts()->orderBy('id', 'desc')->get();
        }

        return (ArticleResource::collection($posts))->additional(["search" => $request->name]);

    }

    /**
     * Get Recently Article
     *
     * @group Articles
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function recentlyPost()
    {
        $posts = Post::orderBy('total_views', 'desc')->limit(5)->get();

        return ArticleResource::collection($posts);
    }

    /**
     * Get All Categories
     *
     * @group Articles
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function categories()
    {
        $categories = Category::all();

        return CategoryResource::collection($categories);
    }

    /**
     * Get All Tags
     *
     * @group Articles
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function tags()
    {
        $tags = Tag::all();

        return TagResource::collection($tags);
    }

}
