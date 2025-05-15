<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServerRequest extends FormRequest
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
            'method' => 'required',
            'name' => 'required',
            'secret_key' => 'nullable',
            'host' => 'nullable',
            'port' => 'nullable',
            'password' => 'nullable'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData();

            $secretKeyPresent = !empty($data['secret_key']);
            $hostPresent = !empty($data['host']);
            $portPresent = !empty($data['port']);
            $passwordPresent = !empty($data['password']);

            if (!$secretKeyPresent && (!$hostPresent || !$portPresent || !$passwordPresent)) {
                $validator->errors()->add('secret_key', 'You must provide either a secret_key or all of host, port, and password.');
                $validator->errors()->add('host', 'You must provide either a secret_key or all of host, port, and password.');
                $validator->errors()->add('port', 'You must provide either a secret_key or all of host, port, and password.');
                $validator->errors()->add('password', 'You must provide either a secret_key or all of host, port, and password.');
            }
        });
    }
}
