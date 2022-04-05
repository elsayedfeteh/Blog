<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Comment\StoreCommentRequest;
use App\Http\Traits\ResponseJsonTrait;
use App\Models\Comment;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CommentController extends Controller
{
    use ResponseJsonTrait;

    /**
     * Add New Comment
     * @group Comments
     *
     * @bodyParam name string Name of Guest. Example: "Ahmed"
     * @bodyParam email email Email of Guest. Example: "Ahmed@app.com"
     * @bodyParam comment string required Content of Comment.
     * @bodyParam post_id id required the id of post.
     * @bodyParam comment_id id the id of comment. Note: "required if you want to replay".
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommentRequest $request)
    {
        $auth_user = auth('api')->user();

        if ($auth_user) {
            $comment = Comment::create([
                'user_id' => $auth_user->id,
                'content' => $request->post('comment'),
                "post_id" => $request->post('post_id'),
                "comment_id" => $request->post('comment_id')
            ]);

            return $this->responseJson(true, 'the commnet add successfully', null, 201);
        } else {
            $guest = Guest::firstOrCreate([
                "name" => $request->post('name'),
                "email" => $request->post('email'),
            ]);

            if ($guest) {
                $comment = Comment::create([
                    'guest_id' => $guest->id,
                    'content' => $request->post('comment'),
                    "post_id" => $request->post('post_id'),
                    "comment_id" => $request->post('comment_id')
                ]);

                return $this->responseJson(true, 'the commnet add successfully', null, 201);
            }
        }
    }
}
