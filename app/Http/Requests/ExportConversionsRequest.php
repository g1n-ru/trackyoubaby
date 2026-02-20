<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportConversionsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }
}
