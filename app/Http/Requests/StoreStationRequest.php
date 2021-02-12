<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStationRequest extends FormRequest
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
            'name' => ['required', 'max:150'],
            'address' => ['nullable', 'max:150'],
            'contact_no' => ['nullable', 'max:50'],
            //
            'pumps.*.pump' => ['nullable', 'max:100'],
            'pumps.*.nozzle' => ['nullable', 'max:100'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Station Name is required',
        ];
    }
}
