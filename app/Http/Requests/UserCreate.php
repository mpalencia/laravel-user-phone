<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreate extends FormRequest
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
            'name'       => 'required',
            'email'       => 'required|email|unique:users',
            'role'          => 'required',
            'password'  => 'required|min:6',
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
