<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTankerLoadRequest extends FormRequest
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
            'trip_no' => ['nullable', 'max:150'],
            'remarks' => ['nullable', 'max:250'],
            'purchase_id' => ['nullable', 'max:50'],
            //
            'details.*.quantity' => ['nullable', 'max:50'],
            'details.*.product_id' => ['required', 'max:50'],
            'details.*.unit_price' => ['nullable', 'max:50'],
        ];
    }

    public function messages()
    {
        return [
            'details.*.product_id.required' => 'Product is required.',
        ];
    }
}
