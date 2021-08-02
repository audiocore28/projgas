<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create depot');
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
                'unique:depots,name,'.$this->id
            ],
            'address' => ['nullable', 'max:150'],
            'email_address' => ['nullable', 'max:100'],
            'contact_person' => ['nullable', 'max:50'],
            'contact_no' => ['nullable', 'max:50'],
        ];
    }
}
