<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AccessoryRequest extends Request
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
        $accessory = $this->route('accessory');

        return [
            'name' => 'required|unique:accessories,name,'.$accessory,
        ];
    }
}
