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
     * Aturan validasi untuk memperbarui data triwulan yang sudah ada.
     */
    public function rules(): array
    {
        return [
            'value'            => ['required', 'numeric', 'min:1', 'max:25'],
            'progres_belum'    => ['nullable', 'integer', 'min:0'],
            'progres_proses'   => ['nullable', 'integer', 'min:0'],
            'progres_sudah'    => ['nullable', 'integer', 'min:0'],
            'calculated_trend' => ['required', 'string'],
            'calculated_level' => ['required'],
        ];
    }

    /**
     * Custom pesan error jika validasi gagal
     */
    public function messages(): array
    {
        return [
            'value.required'            => 'Nilai saat ini wajib diisi.',
            'value.min'                 => 'Nilai minimal 1.',
            'value.max'                 => 'Nilai maksimal 25.',
            'calculated_trend.required' => 'Trend perubahan wajib diisi.',
            'calculated_level.required' => 'Level saat ini wajib diisi.',
        ];
    }
}
