<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayNowRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'enabled' => 'required|boolean',
            'tax_mode' => 'required|in:0,1',
            'store_id' => 'required|string|regex:/^[0-9]+$/|max:255',
            'api_key' => 'required|string|regex:/^[a-zA-Z0-9\-_]+$/|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_square' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
