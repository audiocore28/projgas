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
            'name' => [
                'required',
                'max:100',
                'unique:drivers,name,'.$this->id
            ],
            'nickname' => ['nullable', 'max:50'],
            'address' => ['nullable', 'max:150'],
            'license_no' => ['nullable', 'max:50'],
            'contact_no' => ['nullable', 'max:25'],
        ];
    }
}
