<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGiftRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->merge([
            'start_balance' => str_replace(',', '.', $this->start_balance),
            'end_balance' => str_replace(',', '.', $this->end_balance),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'start_balance' => 'required|regex:/^\d*([\.,]\d{1,2})?$/',
            'end_balance' => 'required|regex:/^\d*([\.,]\d{1,2})?$/',
            'expire_at' => 'required',
            'note' => '',
            'username' => 'nullable|string|min:2|max:24',
        ];
    }
}
