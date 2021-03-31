<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'name' => [
                'required',
                'max:100',
                'unique:clients,name,'.$this->id
            ],
            'office' => ['nullable', 'max:50'],
            'contact_person' => ['nullable', 'max:150'],
            'contact_no' => ['nullable', 'max:50'],
            'email_address' => ['nullable', 'max:150'],
        ];
    }
}
