<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            //'email' => 'required|email',
            'password' => 'required',

            'identity.required' => 'Email or username cannot be empty',
            'email.exists' => 'Email or username already registered',
            'username.exists' => 'Username is already registered',
        ];
    }

    public function messages()
    {
        return [
            //'email.required' => 'البريد الإلكتروني مطلوب.',
           // 'email.email' => 'ادخل عنوان بريد إلكتروني صالح.',
            'identity' => 'required|string',
            'email' => 'string|exists:users',
            'username' => 'string|exists:users',
            'password.required' => 'كلمة المرور مطلوبة.'
        ];
    }
}
