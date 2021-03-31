<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatangasTransactionRequest extends FormRequest
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
            'selectedPurchases' => ['required', 'max:250'],
            'date' => ['required', 'max:50'],
            'trip_no' => ['required', 'max:50'],
            'tanker_id' => ['required', 'max:50'],
            'driver_id' => ['required', 'max:50'],
            'helper_id' => ['required', 'max:50'],
            // 'purchase_id' => ['nullable', 'max:50'],
            //
            'details.*.date' => ['required', 'max:50'],
            'details.*.dr_no' => ['nullable', 'max:50'],
            'details.*.product_id' => ['required', 'max:50'],
            'details.*.client_id' => ['required', 'max:50'],
            'details.*.quantity' => ['nullable', 'max:50'],
            'details.*.unit_price' => ['nullable', 'max:50'],
        ];
    }

    public function messages()
    {
        return [
            'selectedPurchases.required' => 'Purchase no(s). is required',
            'date.required' => 'Date is required',
            'trip_no.required' => 'Trip no. is required',
            'tanker_id.required' => 'Tanker is required',
            'driver_id.required' => 'Driver is required',
            'helper_id.required' => 'Helper is required',
            //
            'details.*.date.required' => 'Date is required',
            'details.*.product_id.required' => 'Product is required',
            'details.*.client_id.required' => 'Client is required',
        ];
    }
}
