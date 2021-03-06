<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Models\Category;
use App\Models\Guest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

use function PHPUnit\Framework\isEmpty;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::active()->paginate(5);

        return view('website.posts.index', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);

        if (Auth::user()) {
            if (Auth::user()->type != 'admin' and Auth::user() != $post->user) {
                $post->increment('total_views');
            }
        } else {
            $post->increment('total_views');
        }
        return view('website.posts.show', compact('post'));
    }

    public function search(SearchRequest $request)
    {
        if ($request->name != null) {
            $posts = Post::active()->where('title', 'Like', '%' . $request->name . '%')
                ->orderBy('id', 'desc')->paginate(10);
        }

        if ($request->category != null) {
            $posts = Category::where('name', $request->category)->first()->posts()->orderBy('id', 'desc')->paginate(10);
        }

        if ($request->tag != null) {
            $posts = Tag::where('name', $request->tag)->first()->posts()->orderBy('id', 'desc')->paginate(10);
        }

        return view('website.posts.index', [
            "posts" => $posts,
            "search" => $request->name,
        ]);
    }
}
