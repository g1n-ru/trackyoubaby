<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientIdRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'click_id' => ['required', 'uuid'],
            'ym_uid' => ['required', 'string', 'max:255'],
        ];
    }
}
