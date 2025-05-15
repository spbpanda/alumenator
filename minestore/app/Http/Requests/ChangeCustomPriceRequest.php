<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class ChangeCustomPriceRequest extends FormRequest
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

    public function messages(): array
    {
        return [
            'price.required' => 'The price field is required.',
            'price.min' => 'The price must be at least :min.',
            'price.regex' => 'The price format is invalid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'price' => str_replace(',', '.', $this->price),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'price' => 'required|min:0|regex:/^\d*([\.,]\d{1,2})?$/',
        ];
    }
}
