<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepMonitoringPeriodRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan membuat request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi yang diterapkan ke request saat proses Update/Edit.
     */
    public function rules(): array
    {
        return [
            'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'created_at'      => ['required', 'date'],
            'risk_event_deta' => ['required', 'string'],
            'status'          => ['required', 'in:0,1'],
            'type'            => ['required', 'in:Proyek,Non-Proyek'],

            // Validasi Inheren
            'inherent'        => ['required', 'numeric', 'min:1', 'max:25'],
            'id_level'        => ['required'],

            // Validasi Target (Sudah disesuaikan dengan form baru)
            'target_value'    => ['required', 'numeric', 'min:1', 'max:25'],
            'target_id_level' => ['required'],
        ];
    }

    /**
     * (Opsional) Custom pesan error jika validasi gagal
     */
    public function messages(): array
    {
        return [
            'id_unit.required'         => 'Unit Kerja wajib diisi.',
            'id_kategori.required'     => 'Kategori wajib diisi.',
            'risk_event_deta.required' => 'Peristiwa resiko wajib diisi.',
            'inherent.required'        => 'Nilai Inheren wajib diisi.',
            'target_value.required'    => 'Nilai Target wajib diisi.',
            'target_id_level.required' => 'Level Target wajib diisi.',
        ];
    }
}
