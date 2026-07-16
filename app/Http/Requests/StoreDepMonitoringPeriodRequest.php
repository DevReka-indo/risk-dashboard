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
            'penanganan'       => ['required', 'in:Belum,Proses,Sudah'],
            'calculated_trend' => ['required', 'string'],
            'calculated_level' => ['required'],
        ];
    }
}
