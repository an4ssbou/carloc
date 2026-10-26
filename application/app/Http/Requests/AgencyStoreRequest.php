<?php

namespace App\Http\Requests;

use App\Models\Agency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgencyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^([A-Za-zÀ-ÿ][A-Za-z0-9\s\-\']*$)/u'
            ],
            'city' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}]+(?:[ -][\p{L}]+)*$/u',
                'min:4',
            ],
            'address' => [
                'required',
                'string',
                'max:255',
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^0[6-7][0-9]{8}$/',
                Rule::unique(Agency::class)
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(Agency::class),
            ],
            'logo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The agency name is required.',
            'name.string' => 'The agency name must be a string.',
            'name.max' => 'The agency name may not be greater than 255 characters.',
            'name.regex' => 'The agency name must start with a letter and can only contain letters, numbers, spaces, hyphens, and apostrophes.',

            'city.required' => 'The city is required.',
            'city.string' => 'The city must be a string.',
            'city.max' => 'The city may not be greater than 255 characters.',
            'city.regex' => 'The city can only contain letters and spaces.',
            'city.min' => 'The city must be at least 4 characters.',

            'address.required' => 'The address is required.',
            'address.string' => 'The address must be a string.',
            'address.max' => 'The address may not be greater than 255 characters.',

            'phone.required' => 'The phone number is required.',
            'phone.string' => 'The phone number must be a string.',
            'phone.regex' => 'The phone number format is invalid. It should start with 06 or 07 followed by 8 digits.',

            'email.required' => 'The email address is required.',
            'email.string' => 'The email address must be a string.',
            'email.email' => 'The email address format is invalid.',
            'email.max' => 'The email address may not be greater than 255 characters.',
            'email.unique' => 'This email address is already associated with another agency.',

            'logo.required' => 'A logo image is required.',
            'logo.image' => 'The logo must be an image file.',
            'logo.mimes' => 'The logo must be a file of type: jpg, jpeg, png.',
            'logo.max' => 'The logo may not be greater than 2MB.',

            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 1000 characters.',
        ];
    }
}
