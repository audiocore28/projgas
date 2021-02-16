<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
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
            'details.*.quantity' => ['nullable', 'max:50'],
            'details.*.unit_price' => ['nullable', 'max:50'],
            'details.*.remarks' => ['nullable', 'max:100'],
            'details.*.purchase_id' => ['nullable', 'max:50'],
            'details.*.product.id' => ['required', 'max:50'],
        ];
    }

    public function messages()
    {
        return [
            'purchase_no.required' => 'Purchase no. is required',
            'details.*.product.id.required' => 'Product is required.',
        ];
    }
}
