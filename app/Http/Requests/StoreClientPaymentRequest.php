<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientPaymentRequest extends FormRequest
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
            'date' => ['required', 'max:50'],
            'client.id' => ['required', 'max:50'],
            'mode' => ['nullable', 'max:100'],
            'amount' => ['required', 'max:50'],
            'remarks' => ['nullable', 'max:150'],
        ];
    }
}
