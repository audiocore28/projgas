<?php

namespace App\Http\Requests;
use App\Models\Helper;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHelperRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update helper', Helper::class, $this->helper);
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
                'unique:helpers,name,'.$this->id
            ],
            'nickname' => ['nullable', 'max:50'],
            'address' => ['nullable', 'max:150'],
            'contact_no' => ['nullable', 'max:25'],
        ];
    }
}
