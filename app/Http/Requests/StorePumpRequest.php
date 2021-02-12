<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePumpRequest extends FormRequest
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
            'station_id' => ['nullable', 'max:50'],
            'pump' => ['nullable', 'max:50'],
            'nozzle' => ['nullable', 'max:50'],
            '*.product_id' => ['required', 'max:50'],
        ];
    }

    public function messages()
    {
        return [
            '*.product_id.required' => 'Product is required.',
        ];
    }
}
