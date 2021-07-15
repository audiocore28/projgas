<?php

namespace App\Http\Requests;
use App\Models\Driver;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update driver', Driver::class, $this->driver);
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
