<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHaulDetailRequest extends FormRequest
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
            // 'title' => ['required', 'max:50'],
            'haul_id' => ['nullable', 'max:50'],
            '*.product_id' => ['required', 'max:50'],
            '*.client_id' => ['required', 'max:50'],
            'quantity' => ['nullable', 'max:50'],
            'unit_price' => ['nullable', 'max:50'],
        ];
    }

    public function messages()
    {
        return [
            '*.product_id.required' => 'Product is required.',
            '*.client_id.required' => 'Client is required.',
        ];
    }
}
