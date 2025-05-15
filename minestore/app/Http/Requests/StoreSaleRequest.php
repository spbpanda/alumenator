<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
            'discount' => str_replace(',', '.', $this->discount),
            'min_basket' => str_replace(',', '.', $this->min_basket),
            'is_advert' => $this->is_advert === 'on' ? 1 : 0,
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
            'discount' => 'required|min:0|max:100|regex:/^\d*([\.,]\d{1,2})?$/',
            'min_basket' => 'required|min:0|regex:/^\d*([\.,]\d{1,2})?$/',
            'start_at' => 'required',
            'expire_at' => 'required',
            'apply_type' => 'required',
            'apply_categories' => 'required_if:apply_type,1',
            'apply_items' => 'required_if:apply_type,2',
            'is_advert' => '',
            'advert_title' => '',
            'advert_description' => '',
            'button_name' => '',
            'button_url' => '',
        ];
    }
}
