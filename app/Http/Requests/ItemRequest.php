<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ItemRequest extends Request
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
        $item = $this->route('item');

        return [
            'product_id' => 'required|unique:items,product_id,'.$item,
            'name' => 'required|unique:items,name,'.$item,
            'unit' => 'required',
        ];
    }
}
