<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChargebackSettingsRequest extends FormRequest
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
            'cb_username'=>$this->get('cb_username') == 'on' ? 1 : 0,
            'cb_ip' => $this->get('cb_ip') == 'on' ? 1 : 0
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
            'cb_threshold' => 'required',
            'cb_period' => 'required',
            'cb_username' => 'required|boolean',
            'cb_ip' => 'required|boolean',
            'cb_bypass' => 'required',
            'cb_local' => 'required',
        ];
    }
}
