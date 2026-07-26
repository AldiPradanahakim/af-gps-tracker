<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {

        if ($this->isMethod('post')) {
            return [

                'name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'phone' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8),
                ],

            ];
        }





        /** @var User $user */
        $user = $this->user();

        return [

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'current_password' => [
                'nullable',
                'required_with:password',
            ],

            'password' => [
                'nullable',
                'confirmed',
                Password::min(8),
            ],

        ];
    }
}
