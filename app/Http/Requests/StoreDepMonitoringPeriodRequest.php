<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepMonitoringPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quarter'          => ['required', 'in:TW1,TW2,TW3,TW4'],
            'year'             => ['required', 'integer', 'min:2020', 'max:2099'],
            'value'            => ['required', 'numeric', 'min:1', 'max:25'],
            'progres_belum' => 'nullable|integer|min:0',
            'progres_proses' => 'nullable|integer|min:0',
            'progres_sudah' => 'nullable|integer|min:0',
            'calculated_trend' => ['required', 'string'],
            'calculated_level' => ['required'],

            // Validasi Inheren
            'inherent'        => ['required', 'numeric', 'min:1', 'max:25'],
            'id_level'        => ['required'],

            // Validasi Target (Ini yang penting!)
            'target_value'    => ['required', 'numeric', 'min:1', 'max:25'],
            'target_id_level' => ['required'],
        ];
    }
}
