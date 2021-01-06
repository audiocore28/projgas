<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
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
            'date' => ['nullable', 'max:50'],
            'purchase_no' => ['required', 'max:150'],
            'supplier_id' => ['nullable', 'max:50'],
            //
            // 'product_id' => ['nullable', 'max:50'],
            // 'quantity' => ['nullable', 'max:50'],
            // 'unit_price' => ['nullable', 'max:50'],
            // 'amount' => ['nullable', 'max:50'],
            // 'remarks' => ['nullable', 'max:50'],
        ];
    }
}
