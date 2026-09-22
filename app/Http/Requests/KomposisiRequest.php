<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KomposisiRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'komposisi' => ['nullable', 'array'],
            'komposisi.*.bahan_id' => ['required', 'distinct', 'exists:bahan,id'],
            'komposisi.*.jumlah' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
