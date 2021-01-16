<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTankerLoadRequest extends FormRequest
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
            'date' => ['required', 'max:50'],
            'remarks' => ['nullable', 'max:250'],
            'purchase_id' => ['nullable', 'max:50'],
            'tanker_id' => ['required', 'max:50'],
            'driver_id' => ['required', 'max:50'],
            'helper_id' => ['nullable', 'max:50'],
        ];
    }
}
