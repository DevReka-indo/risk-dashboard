<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTopMonitoringRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bulan' => [
                'required',
                'integer',
                'between:1,12',
                Rule::unique('top_monitoring_bulanan', 'bulan')
                    ->where('tahun', $this->integer('tahun'))
                    ->where('id_risiko', $this->route('topRisk')->id_risiko)
                    ->ignore($this->route('monitoring')->id_monitoring, 'id_monitoring'),
            ],
            'tahun'          => ['required', 'integer', 'digits:4', 'min:2000'],
            'nilai'          => ['required', 'integer', 'between:1,25'],
            'status'         => ['required', Rule::in(['Aktif', 'Tidak Aktif'])],
            'progres_belum'  => ['nullable', 'integer', 'min:0'],
            'progres_proses' => ['nullable', 'integer', 'min:0'],
            'progres_sudah'  => ['nullable', 'integer', 'min:0'],
            'catatan'        => ['nullable', 'string'],
        ];
    }
}
