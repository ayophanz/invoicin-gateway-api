<?php

namespace App\Http\Requests\Register;

use App\Http\Requests\BaseRequest;

class StoreRequest extends BaseRequest
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
            'image.*'   => 'base64mimes:png,jpg,jpeg',
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|confirmed|min:6',
        ];
    }
}
