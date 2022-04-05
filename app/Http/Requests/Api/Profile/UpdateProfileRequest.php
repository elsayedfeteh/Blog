<?php

namespace App\Http\Requests\Api\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
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
            'name' => 'sometimes|required|string|unique:users,name,'. auth('api')->user()->id,
            'email' => 'sometimes|required|email|unique:users,email,'. auth('api')->user()->id,
            'phone' => 'nullable|numeric',
            'image' => 'nullable|image',
            'description' => 'nullable|string|max:200',
            'gender' => 'nullable|in:male,female',
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
