<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Validation\Rule;


class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'\-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'\-]+$/'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class),],
            'phone' => ['required', 'string', 'regex:/^0[6-7][0-9]{8}$/', Rule::unique(User::class),],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'The first name is required.',
            'last_name.required' => 'The last name is required.',
            'email.required' => 'The email address is required.',
            'phone.required' => 'The phone number is required.',
            'password.required' => 'The password is required.',
            'first_name.regex' => 'The first name may only contain letters, spaces, apostrophes, and hyphens.',
            'last_name.regex' => 'The last name may only contain letters, spaces, apostrophes, and hyphens.',
            'phone.regex' => 'The phone number must start with 06 or 07 and be followed by 8 digits.',
            'email.unique' => 'The email address is already registered.',
            'phone.unique' => 'The phone number is already registered.',
        ];
    }
}
