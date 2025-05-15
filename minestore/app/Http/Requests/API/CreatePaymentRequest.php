<?php

namespace App\Http\Requests\API;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
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
        $settings = Setting::query()->select('details')->find(1);
        if ($settings->details == 0) {
            return [
                'details.fullname' => 'nullable|string',
                'details.email' => 'nullable|email',
                'details.address1' => 'nullable|string',
                'details.address2' => 'nullable|string',
                'details.city' => 'nullable|string',
                'details.region' => 'nullable|string|max:30',
                'details.country' => 'nullable|string',
                'details.zipcode' => 'nullable|string',
                'termsAndConditions' => 'required|boolean|in:true',
                'privacyPolicy' => 'required|boolean|in:true',
                'paymentMethod' => 'required|string',
            ];
        } else {
            return [
                'details.fullname' => 'required|string',
                'details.email' => 'required|email',
                'details.address1' => 'required|string',
                'details.address2' => 'nullable|string',
                'details.city' => 'required|string',
                'details.region' => 'required|string|max:30',
                'details.country' => 'required|string',
                'details.zipcode' => 'required|string',
                'termsAndConditions' => 'required|boolean|in:true',
                'privacyPolicy' => 'required|boolean|in:true',
                'paymentMethod' => 'required|string',
            ];
        }
    }
}
