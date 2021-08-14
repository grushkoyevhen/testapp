<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class CreateUserRequest extends FormRequest
{
    public function validationData()
    {
        return $this->post();
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'bail',
                'min:5',
                'max:30',
            ],
            'email' => [
                'required',
                'email:filter',
                'max:100',
                'unique:App\Models\User,email'
            ],
            'password' => [
                'required',
                'max:15',
                'confirmed',
                Password::min(8)->letters()
            ],
        ];
    }

    protected function prepareForValidation()
    {
        if(array_key_exists('name', $this->post()))
            $this->merge(['name', strtolower($this->post('name'))]);
    }

    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag('regUser')
            ->redirectTo(url()->route('reg.index'));
    }
}
