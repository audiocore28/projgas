<?php

namespace App\Http\Requests;
use App\Models\MonthlyBatangasTransaction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMonthlyBatangasTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update batangas transaction', MonthlyBatangasTransaction::class, $this->monthly_batangas_transaction);
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
            'transactions.*.tanker.id' => ['required', 'max:50'],
            'transactions.*.driver.id' => ['required', 'max:50'],
            'transactions.*.helper.id' => ['required', 'max:50'],
            'transactions.*.driver_salary' => ['nullable', 'max:50'],
            'transactions.*.helper_salary' => ['nullable', 'max:50'],

            // BatangasTransactionDetail
            'transactions.*.details.*.date' => ['required', 'max:50'],
            'transactions.*.details.*.client_id' => ['required', 'max:50'],
            'transactions.*.details.*.remarks' => ['nullable', 'max:50'],
            'transactions.*.details.*.product_id' => ['required', 'max:50'],
            'transactions.*.details.*.quantity' => ['nullable', 'max:50'],
            'transactions.*.details.*.unit_price' => ['nullable', 'max:50'],

            // TankerLoadDetail
            'transactions.*.tanker_loads.*.tanker_load_details.*.unit_price' => ['nullable', 'max:50'],

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

            // BatangasTransactionDetail
            'transactions.*.details.*.date.required' => 'Date is required',
            'transactions.*.details.*.client_id.required' => 'Client is required',
            'transactions.*.details.*.product_id.required' => 'Product is required',
        ];
    }
}
