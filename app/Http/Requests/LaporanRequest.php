<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class LaporanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ];
    }

    // Tanpa filter: bulan berjalan
    public function dari(): Carbon
    {
        return $this->validated('dari') ? Carbon::parse($this->validated('dari')) : today()->startOfMonth();
    }

    public function sampai(): Carbon
    {
        return $this->validated('sampai') ? Carbon::parse($this->validated('sampai')) : today()->endOfMonth()->startOfDay();
    }
}
