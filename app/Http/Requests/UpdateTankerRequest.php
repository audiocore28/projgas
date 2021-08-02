<?php

namespace App\Http\Requests;
use App\Models\Tanker;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTankerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update tanker', Tanker::class, $this->tanker);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'plate_no' => [
                'required',
                'max:40',
                'unique:tankers,plate_no,'.$this->id
            ],
            'compartment' => ['nullable', 'max:40'],
        ];
    }
}
