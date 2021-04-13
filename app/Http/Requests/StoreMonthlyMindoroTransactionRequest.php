<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMonthlyMindoroTransactionRequest extends FormRequest
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
            'date.year' => ['required', 'max:50'],
            'date.month' => ['required', 'max:50'],
            //
            'transactions.*.trip_no.*' => ['required', 'max:50'],
            'transactions.*.tanker_id.*' => ['required', 'max:50'],
            'transactions.*.driver_id.*' => ['required', 'max:50'],
            'transactions.*.helper_id.*' => ['required', 'max:50'],
        ];
    }

    public function messages()
    {
        return [
            'date.year.required' => 'Year is required',
            'date.month.required' => 'Month is required',
            //
            'transactions.*.trip_no.*.required' => 'Trip no. is required',
            'transactions.*.tanker_id.*.required' => 'Tanker is required',
            'transactions.*.driver_id.*.required' => 'Driver is required',
            'transactions.*.helper_id.*.required' => 'Helper is required',
        ];
    }
}
