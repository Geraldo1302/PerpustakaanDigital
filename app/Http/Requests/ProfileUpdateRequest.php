<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\PesanValidasi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                // abaikan email milik akun sendiri
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'no_telepon' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return PesanValidasi::pesan();
    }

    public function attributes(): array
    {
        return PesanValidasi::atribut();
    }
}
