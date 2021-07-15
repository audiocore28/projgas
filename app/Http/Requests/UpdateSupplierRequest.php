<?php

namespace App\Http\Requests;
use App\Models\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update supplier', Supplier::class, $this->supplier);
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
                'unique:suppliers,name,'.$this->id
            ],
            'office' => ['nullable', 'max:150'],
            'email_address' => ['nullable', 'max:100'],
            'contact_person' => ['nullable', 'max:50'],
            'contact_no' => ['nullable', 'max:50'],
        ];
    }
}
