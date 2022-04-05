<?php

namespace App\Http\Requests\Api\Comment;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [Rule::requiredIf(! auth('api')->user()) , 'string'],
            'email' => [Rule::requiredIf(! auth('api')->user()) , 'email'],
            'comment' => 'required|string',
            'post_id' => 'required|exists:posts,id',
            'comment_id' => 'nullable|exists:comments,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors(),
        ]), 422);
    }
}
