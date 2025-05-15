<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GlobalCommandsSaveRequest extends FormRequest
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
            'enable_globalcmd' => 'nullable',
            'command' => 'nullable|array',
            'command.*.cmd' => 'required',
            'command.*.servers' => 'required',
            'command.*.is_online' => 'nullable',
            'command.*.price' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'command.*.cmd.required' => 'Command is required',
            'command.*.servers.required' => 'Servers is required',
            'command.*.price.required' => 'Price is required',
        ];
    }
}
