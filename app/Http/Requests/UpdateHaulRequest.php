<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHaulRequest extends FormRequest
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
            'trip_no' => ['nullable', 'max:50'],
            'tanker_id' => ['required', 'max:50'],
            'driver_id' => ['nullable', 'max:50'],
            'helper_id' => ['nullable', 'max:50'],
            'purchase_id' => ['nullable', 'max:50'],
            //
            'details.*.product_id' => ['required', 'max:50'],
            'details.*.client_id' => ['required', 'max:50'],
            'details.*.quantity' => ['nullable', 'max:50'],
            'details.*.unit_price' => ['nullable', 'max:50'],
        ];
    }

    public function messages()
    {
        return [
            'tanker_id.required' => 'Tanker is required',
            'details.*.product_id.required' => 'Product is required.',
            'details.*.client_id.required' => 'Client is required.',
        ];
    }
}
