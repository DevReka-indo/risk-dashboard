<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTopMonitoringRequest extends FormRequest
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
                    ->where('id_risiko', $this->route('topRisk')->id_risiko),
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

    public function messages(): array
    {
        return [
            'bulan.unique' => 'Data monitoring untuk bulan dan tahun ini sudah pernah diinputkan.',
            'nilai.between' => 'Nilai realisasi risiko harus berada di rentang 1 - 25.',
        ];
    }
}
