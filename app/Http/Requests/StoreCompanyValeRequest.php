<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyValeRequest extends FormRequest
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
            'station_transaction_id' => ['nullable', 'max:50'],
            'pump_no' => ['nullable', 'max:100'],
            'voucher_no' => ['nullable', 'max:100'],
            '*.company_id' => ['required', 'max:50'],
            '*.product_id' => ['required', 'max:50'],
            'quantity' => ['nullable', 'max:50'],
            'remarks' => ['nullable', 'max:500'],
        ];
    }

    public function messages()
    {
        return [
            '*.client_id.required' => 'Client is required',
            '*.product_id.required' => 'Product is required',
        ];
    }
}
