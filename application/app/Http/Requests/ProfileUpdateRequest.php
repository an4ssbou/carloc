<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
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
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'\-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'\-]+$/'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => [
                'required', 
                'string', 
                'regex:/^0[6-7][0-9]{8}$/',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'cin_number' => [
                'nullable',
                'string',
                'regex:/^[A-Z]{1,2}[0-9]{6}$/',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'profile_photo' => [
            'nullable',                    
            'image',                       
            'mimes:jpg,jpeg,png',     
            'max:2048',                    
            ],
            'remove_profile_photo' => ['nullable', 'boolean'],
            'remove_license_photo' => ['nullable', 'boolean'],
            'remove_cin_photo' => ['nullable', 'boolean'],
            'license_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
            'cin_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First Name is required',
            'first_name.regex' => 'First Name can only contain letters, spaces, apostrophes, and hyphens',
            'last_name.regex' => 'Last Name can only contain letters, spaces, apostrophes, and hyphens',
            'last_name.required' => 'Last Name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email is already in use',
            'phone.required' => 'Phone is required',
            'phone.regex' => 'Invalid phone number format',
            'phone.unique' => 'Phone number is already in use',
            'cin_number.regex' => 'Invalid CIN format',
            'cin_number.unique' => 'CIN number is already in use',
            'profile_photo.image' => 'The profile photo must be an image',
            'profile_photo.mimes' => 'The profile photo must be a file of type: jpg, jpeg, png',
            'profile_photo.max' => 'The profile photo may not be greater than 2MB',
            'license_photo.image' => 'The license photo must be an image',
            'license_photo.mimes' => 'The license photo must be a file of type: jpg, jpeg, png',
            'license_photo.max' => 'The license photo may not be greater than 2MB',
            'cin_photo.image' => 'The CIN photo must be an image',
            'cin_photo.mimes' => 'The CIN photo must be a file of type: jpg, jpeg, png',
            'cin_photo.max' => 'The CIN photo may not be greater than 2MB',
        ];
    }
}
