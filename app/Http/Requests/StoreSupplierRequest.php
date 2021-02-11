<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
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
            'name' => ['required', 'max:50'],
            'office' => ['nullable', 'max:150'],
            'email_address' => ['nullable', 'max:100'],
            'contact_person' => ['nullable', 'max:50'],
            'contact_no' => ['nullable', 'max:50'],
        ];
    }
}
