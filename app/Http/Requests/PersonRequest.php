<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PersonRequest extends Request
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
            'cust_id' => 'required|unique:people,cust_id,'.$person,
            'company' => 'required',
            'name'=>'min:3',
            'roc_no' => 'unique:people,roc_no,'.$person,
            'contact'=>array('regex:/^([0-9\s\-\+\(\)]*)$/'),
            'alt_contact'=>array('regex:/^([0-9\s\-\+\(\)]*)$/'),
            'postcode' => 'numeric',
            'cost_rate' => 'numeric',
            'key_lock_number' => 'numeric'
        ];
    }

    public function messages()
    {
        return [
            'cust_id.required' => 'Please fill in the Customer ID',
            'cust_id.unique' => 'The ID has been taken',
            'company.required' => 'Please fill in the Customer ID Name',
            'name.min' => 'Attn To must more than 3 words',
            'roc_no.unique' => 'The ROC No has been taken',
            'contact.regex' => 'The contact number only accepts 0-9, +, -',
            'alt_contact.regex' => 'The Alt contact number only accepts 0-9, +, -',
            'postcode.numeric' => 'The postcode must be in numbers',
            'cost_rate.numeric' => 'Cost rate must be in numbers',
            'key_lock_number.numeric' => 'Key Lock Number must be in numbers',
        ];
    }
}
