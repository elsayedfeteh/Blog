<?php

namespace App\Http\Requests\Api\Role;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRoleRequest extends FormRequest
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
            'name' => ['required', 'string','unique:roles,name'],
            'permissions' => ['required', 'array', function($attribute, $value, $fail) {
                foreach ($value as $key => $value) {
                    if (! in_array($key, array_keys(config('permissions')))){
                        return $fail('you insert invalid permissions');
                    }
                }
            }],
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
