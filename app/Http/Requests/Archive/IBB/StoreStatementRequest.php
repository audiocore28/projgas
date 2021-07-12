<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatementRequest extends FormRequest
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
            'date' => ['nullable', 'max:50'],
            'client_id' => ['nullable', 'max:50'],
            'payment' => ['nullable', 'max:50'],
            'check_no' => ['nullable', 'max:50'],
            'soa_no' => ['nullable', 'max:50'],
            'lists' => ['nullable', 'max:50'],
        ];
    }
}
