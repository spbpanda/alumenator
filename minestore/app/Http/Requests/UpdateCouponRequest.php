<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
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
            'name' => 'required|unique:coupons,name',
            'type' => '',
            'discount_percent' => 'required_if:type,percent|regex:/^\d*([\.,]\d{1,2})?$/',
            'discount_amount' => 'required_if:type,amount|regex:/^\d*([\.,]\d{1,2})?$/',
            'available' => 'nullable|numeric',
            'limit_per_user' => 'numeric',
            'min_basket' => 'regex:/^\d*([\.,]\d{1,2})?$/',
            'apply_type' => 'required',
            'apply_categories' => 'sometimes',
            'apply_items' => 'sometimes',
            'note' => 'sometimes',
            'start_at' => 'required|nullable',
            'expire_at' => 'required|nullable'
        ];
    }
}
