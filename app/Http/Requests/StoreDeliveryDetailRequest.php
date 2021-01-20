<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryDetailRequest extends FormRequest
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
            'delivery_id' => ['nullable', 'max:50'],
            'dr_no' => ['nullable', 'max:50'],
            'product_id' => ['nullable', 'max:50'],
            'client_id' => ['nullable', 'max:50'],
            'quantity' => ['nullable', 'max:50'],
            'unit_price' => ['nullable', 'max:50'],
        ];
    }
}
