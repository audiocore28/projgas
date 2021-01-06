<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
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
            'nickname' => ['nullable', 'max:50'],
            'address' => ['nullable', 'max:150'],
            'license_no' => ['nullable', 'max:50'],
            'dob' => ['nullable', 'max:50'],
            'date_hired' => ['nullable', 'max:50'],
            'status' => ['nullable', 'max:20'],
            'contact_no' => ['nullable', 'max:25'],
        ];
    }
}
