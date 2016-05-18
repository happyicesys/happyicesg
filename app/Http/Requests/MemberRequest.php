<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class MemberRequest extends Request
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

        $user = $this->route('user');

        return [
            'name'=>'required|min:3',
            'company'=>'required|min:3|unique:users,username,'.$user,
            'email'=>'email|unique:users,email,'.$user,
            'contact'=>array('regex:/^([0-9\s\-\+\(\)]*)$/'),
            'alt_contact'=>array('regex:/^([0-9\s\-\+\(\)]*)$/'),
        ];
    }
}
