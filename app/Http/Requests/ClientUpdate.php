<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientUpdate extends FormRequest
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
        $id = \Request::segment(3);
        return [
            'name'       => 'required',
            'email'       => 'email|unique:clients,email,'.$id,
            'authorize'  => 'required',
            'password'  => 'min:6',
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
            'authorize.required'=>"Authorize is required. (0 or 1)",
        ];
    }
}
