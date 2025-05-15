<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateManualPaymentRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required|max:60',
            'price' => ['required', 'regex:/^[\d\.,]+$/'],
            'email' => 'required|email',
            'gateway' => 'nullable',
            'note' => 'nullable',
            'transaction' => 'nullable',
            'packages' => '',
            'send_mail' => '',
            'payment_is_execute' => '',
        ];
    }
}
