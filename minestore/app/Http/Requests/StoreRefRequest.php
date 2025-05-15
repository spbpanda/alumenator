<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefRequest extends FormRequest
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
            'referer' => 'required|unique:ref_codes,referer',
            'code' => 'required',
            'percent' => 'required|regex:/^\d*([\.,]\d{1,2})?$/',
            'cmd' => '',
            'commands' => '',
        ];
    }
}
