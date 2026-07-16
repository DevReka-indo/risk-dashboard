<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepMonitoringPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'value'            => ['required', 'numeric', 'min:1', 'max:25'],
            'penanganan'       => ['required', 'in:Belum,Proses,Sudah'],
            'calculated_trend' => ['required', 'string'],
            'calculated_level' => ['required'],
        ];
    }
}
