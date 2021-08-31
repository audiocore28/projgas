<?php

namespace App\Http\Requests;
use App\Models\MonthlyMindoroTransaction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMonthlyMindoroTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update mindoro transaction', MonthlyMindoroTransaction::class, $this->monthly_mindoro_transaction);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // MonthlyMindoroTransaction
            'date' => ['required', 'max:50'],

            // MindoroTransaction
            'transactions.*.trip_no' => ['required', 'max:50'],
            'transactions.*.tanker.id' => ['required', 'max:50'],
            'transactions.*.driver.id' => ['required', 'max:50'],
            'transactions.*.helper_id' => ['nullable', 'max:50'],
            'transactions.*.expense' => ['required', 'max:50'],
            'transactions.*.driver_salary' => ['nullable', 'max:50'],
            'transactions.*.helper_salary' => ['nullable', 'max:50'],
            'transactions.*.commission' => ['nullable', 'max:50'],

            // MindoroTransactionDetail
            'transactions.*.mindoro_transaction_details.*.date' => ['required', 'max:50'],
            'transactions.*.mindoro_transaction_details.*.client_id' => ['required', 'max:50'],
            'transactions.*.mindoro_transaction_details.*.remarks' => ['nullable', 'max:50'],
            'transactions.*.mindoro_transaction_details.*.product_id' => ['required', 'max:50'],
            'transactions.*.mindoro_transaction_details.*.quantity' => ['nullable', 'max:50'],
            'transactions.*.mindoro_transaction_details.*.unit_price' => ['nullable', 'max:50'],
            'transactions.*.mindoro_transaction_details.*.dr_no' => ['nullable', 'max:80'],

        ];
    }

    public function messages()
    {
        return [
            // MonthlyMindoroTransaction
            'date.required' => 'Month is required',

            // MindoroTransaction
            'transactions.*.trip_no.required' => 'Trip no. is required',
            'transactions.*.tanker.id.required' => 'Tanker is required',
            'transactions.*.driver.id.required' => 'Driver is required',

            // MindoroTransactionDetail
            'transactions.*.mindoro_transaction_details.*.date.required' => 'Date is required',
            'transactions.*.mindoro_transaction_details.*.client_id.required' => 'Client is required',
            'transactions.*.mindoro_transaction_details.*.product_id.required' => 'Product is required',
        ];
    }
}
