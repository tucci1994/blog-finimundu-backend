<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email'       => ['required', 'string', 'email'],
            'password'    => ['required', 'string', 'min:8'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => "L'indirizzo email e' obbligatorio.",
            'email.email'       => 'Inserire un indirizzo email valido.',
            'password.required' => 'La password e\' obbligatoria.',
            'password.min'      => 'La password deve contenere almeno :min caratteri.',
        ];
    }
}
