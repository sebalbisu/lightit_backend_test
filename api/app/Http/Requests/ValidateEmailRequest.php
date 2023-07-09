<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ValidateEmailRequest extends FormRequest
{
    public User $user;

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
            'id' => 'required|integer|exists:users,id',
            'hash' => 'required|string',
            'signature' => 'required|string',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $this->user = User::findOrFail($this->input('id'));
                if (sha1($this->user->email) != $this->input('hash')) {
                    $validator->errors()->add(
                        'hash',
                        'Invalid hash'
                    );
                }
            },
        ];
    }
}
