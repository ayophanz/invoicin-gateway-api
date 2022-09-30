<?php

namespace App\Http\Requests\Register;

use Illuminate\Http\Request;

class PartialRequest extends Request
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
        // if ($request->form_type == 'user') {
        //     return [
        //         'firstname' => 'required',
        //         'lastname'  => 'required',
        //         'email'     => 'required|email',
        //         'password'  => 'required|confirmed|min:6',
        //     ];
        // }

        // if ($request->form_type == 'org') {
        //     return [
        //         'type'  => 'required',
        //         'name'  => 'required',
        //         'email' => 'required|email',
        //     ];
        // }

        // if ($request->form_type == 'orgBillingAddress') {
        //     return [
        //         'address' => 'required',
        //         'city'    => 'required',
        //         'zipcode' => 'required|numeric',
        //         'country' => 'required|numeric'
        //     ];
        // }

        return [];
    }
}
