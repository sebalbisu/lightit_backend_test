<?php

namespace App\Http\Requests;

use App\Rules\Name;
use App\Rules\Phone;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', app(Name::class)],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => ['required', app(Phone::class)],
            'notify_sms' => 'boolean',
            'photo' => 'required|file|mimes:jpg,png|min:100|max:10000',
        ];
    }
}
