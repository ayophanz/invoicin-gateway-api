<?php

namespace App\Http\Requests\Register;

use Illuminate\Http\Request;
use App\Http\Requests\BaseRequest;

class PartialRequest extends BaseRequest
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
    public function rules(Request $request)
    {
        if ($request->form_type == 'user') {
            return [
                'firstname' => 'required',
                'lastname'  => 'required',
                'email'     => 'required|email',
                'password'  => 'required|confirmed|min:6',
            ];
        }

        if ($request->form_type == 'org') {
            return [
                'type'     => 'required',
                'name'     => 'required',
                'orgEmail' => 'required|email',
            ];
        }

        if ($request->form_type == 'orgBillingAddress') {
            return [
                'address' => 'required',
                'city'    => 'required',
                'zipcode' => 'required|numeric',
                'country' => 'required|numeric',
            ];
        }

        return [
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|email',
            'password'  => 'required|confirmed|min:6',
            'type'      => 'required',
            'name'      => 'required',
            'orgEmail'  => 'required|email',
            'address'   => 'required',
            'city'      => 'required',
            'zipcode'   => 'required|numeric',
            'country'   => 'required|numeric'
        ];
    }
}
