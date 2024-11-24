<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required',
            'is_active' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->route('user'),
            'cpf' => 'required|max:14|unique:users,cpf,' . $this->route('user'),
            'password' => 'nullable|min:6|confirmed',
        ];
    }
}
