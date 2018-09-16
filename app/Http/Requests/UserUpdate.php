<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdate extends FormRequest
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
        $uri3 = \Request::segment(3);
        return [
            'password'  => 'min:6',
            'role'          => 'required',
            'name'        => 'required',
            'email'        => 'email|unique:users,email,'.$uri3,
        ];
    }

    /**
     * Custom messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'role.required'=>"role is required. ('admin' or 'non-admin')",
        ];
    }

}
