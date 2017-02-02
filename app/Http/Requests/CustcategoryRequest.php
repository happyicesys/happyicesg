<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CustcategoryRequest extends Request
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
        $custcat = $this->route('custcat');

        return [
            'name' => 'required|unique:custcategories,name,'.$custcat,
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Please fill in the name',
            'name.unique' => 'The name was in used, please try another'
        ];
    }
}
