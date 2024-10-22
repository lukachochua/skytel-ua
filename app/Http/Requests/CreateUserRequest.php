<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['nullable', 'string', 'min:8', 'max:32', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:8', 'max:32',],
            'avatar' => ['nullable', 'string'],
            'google_id' => ['nullable', 'string'],
            'facebook_id' => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'An email is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'This email is already taken.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
