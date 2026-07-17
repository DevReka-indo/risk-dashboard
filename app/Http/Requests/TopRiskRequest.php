<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class TopRiskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Validasi berdasarkan kode sumber.
        return [
            'nama_peristiwa_risiko' => ['required', 'string', 'max:255'],
            'id_kategori' => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'tanggal_dibuat' => ['required', 'date'],
            'is_aktif' => ['nullable', 'boolean'],
            'unit_kerja' => ['required', 'array', 'min:1'],
            'unit_kerja.*' => ['integer', 'exists:top_unit_kerja,id_unit'],
        ];
    }
}
