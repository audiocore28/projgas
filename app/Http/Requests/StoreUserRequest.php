<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create user');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => [
                'required',
                'max:100',
                'unique:users,email,'.$this->id
            ],
            'password' => ['nullable'],
            // 'owner' => ['required', 'boolean'],
            // 'photo' => ['nullable', 'image'],
        ];
    }
}
