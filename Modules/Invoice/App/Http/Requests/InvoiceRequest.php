<?php

namespace Modules\Invoice\App\Http\Requests;

use App\Rules\Captcha;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string',
            'business_name' => 'nullable|string|max:255',
            'business_email' => 'nullable|email|max:255',
            'business_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:2000',
            'logo' => 'nullable|string',
            'currency' => 'nullable|string|max:50',
        ];

        if (\Modules\GlobalSetting\App\Models\GlobalSetting::where('key', 'recaptcha_status')->first()?->value == 1) {
            $rules['g-recaptcha-response'] = new Captcha();
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'client_name.required' => trans('translate.Client name is required'),
            'items.required' => trans('translate.At least one line item is required'),
            'items.*.description.required' => trans('translate.Item description is required'),
            'items.*.quantity.required' => trans('translate.Quantity is required'),
            'items.*.unit_price.required' => trans('translate.Unit price is required'),
        ];
    }
}
