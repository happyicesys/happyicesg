<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ClientRegisterRequest extends Request
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
        $person = $this->route('person');

        return [
            'salutation' =>'required',
            'name' => 'required|min:5',
            'contact'=>'required|digits_between:8,11',
            'del_postcode'=>'required|digits:6',
            'del_address'=>'required',
            'email'=>'email|unique:people,email,'.$person,
            'password'=>'required|confirmed',
            'password_confirmation'=>'required',
        ];
    }
}
