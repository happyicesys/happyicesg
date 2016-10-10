<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class D2dOnlineSaleRequest extends Request
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
            'sequence' => 'integer',
            'item_id' => 'required',
            'caption' => 'required',
            'unit_price' => 'numeric|required',
            'qty_divisor' => 'numeric|min:1'
        ];
    }

    public function messages()
    {
        return [
            'sequence.integer' => 'Sequence number must be a whole number',
            'item_id.required' => 'Please select an item',
            'caption.required' => 'Please fill in the caption',
            'unit_price.numeric' => 'Unit price must be in numbers',
            'unit_price.required' => 'Please fill in the unit price',
            'qty_divisor.numeric' => 'Qty divisor must be in numbers',
            'qty_divisor.min' => 'Qty divisor must be greater than 1'
        ];
    }
}
