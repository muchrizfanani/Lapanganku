<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveVenueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'pemilik';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'sport' => ['required', 'string', 'max:80'],
            'price' => ['required', 'integer', 'min:0'],
            'location' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'facilities' => ['nullable', 'string', 'max:2000'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'reviews' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:available,limited,full'],
            'gradient' => ['nullable', 'string', 'max:255'],
            'sport_badge' => ['nullable', 'string', 'max:100'],
        ];
    }
}
