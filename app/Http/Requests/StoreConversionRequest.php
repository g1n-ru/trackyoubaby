<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'click_id' => ['required', 'uuid'],
            'target' => ['required', 'string', 'max:255'],
            'revenue' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'order_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}
