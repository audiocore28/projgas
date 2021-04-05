<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePumpReadingRequest extends FormRequest
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
            'pump' => ['nullable', 'max:100'],
            'opening' => ['nullable', 'max:500'],
            'closing' => ['nullable', 'max:500'],
            'unit_price' => ['nullable', 'max:50'],
        ];
    }
}
