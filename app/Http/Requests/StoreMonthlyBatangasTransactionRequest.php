<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMonthlyBatangasTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create batangas transaction');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // MonthlyBatangasTransaction
            'date' => ['required', 'max:50'],

            // BatangasTransaction
            'transactions.*.trip_no' => ['required', 'max:50'],
            'transactions.*.tanker_id' => ['required', 'max:50'],
            'transactions.*.driver_id' => ['required', 'max:50'],
            'transactions.*.helper_id' => ['required', 'max:50'],
        ];
    }

    public function messages()
    {
        return [
            // MonthlyBatangasTransaction
            'date.required' => 'Month is required',

            // BatangasTransaction
            'transactions.*.trip_no.required' => 'Trip no. is required',
            'transactions.*.tanker_id.required' => 'Tanker is required',
            'transactions.*.driver_id.required' => 'Driver is required',
            'transactions.*.helper_id.required' => 'Helper is required',
        ];
    }
}
