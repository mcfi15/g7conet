<?php

namespace Modules\QRCode\App\Http\Requests;

use App\Rules\Captcha;
use Illuminate\Foundation\Http\FormRequest;

class QRCodeRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'content' => 'required|string|max:2000',
            'foreground_color' => 'nullable|string|max:7',
            'background_color' => 'nullable|string|max:7',
            'size' => 'nullable|integer|min:100|max:1000',
            'format' => 'nullable|string|in:png,jpeg',
            'dot_style' => 'nullable|string|in:square,rounded,dots,classy,extra-rounded',
            'eye_style' => 'nullable|string|in:square,rounded,circle',
            'eye_border_color' => 'nullable|string|max:7',
            'eye_center_color' => 'nullable|string|max:7',
            'gradient_enabled' => 'nullable|boolean',
            'gradient_start' => 'nullable|string|max:7',
            'gradient_end' => 'nullable|string|max:7',
            'frame_style' => 'nullable|string|in:panel,outline,shadow,bar',
            'frame_text' => 'nullable|string|max:30',
            'frame_color' => 'nullable|string|max:7',
            'frame_text_color' => 'nullable|string|max:7',
            'frame_margin' => 'nullable|integer|min:0|max:10',
            'frame_font_size' => 'nullable|integer|min:10|max:30',
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
            'content.required' => trans('translate.Content is required'),
        ];
    }
}
