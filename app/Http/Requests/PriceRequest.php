<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PriceRequest extends Request
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
        $price = $this->route('price');

        return [
            'item_id' => 'unique:prices,item_id,null,id,person_id,'.$this->person_id,
            'retail_price' => 'regex:/^[0-9]+(\.[0-9]{1,2})?$/',
            'quote_price' => 'regex:/^[0-9]+(\.[0-9]{1,2})?$/',
            //'quote_price' => array( 'required_without_all:retail_price', 'regex:/^[0-9]+(\.[0-9]{1,2})?$/'),
        ];
    }
}
