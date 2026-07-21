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
        return [
            'nama_peristiwa_risiko' => ['required', 'string'],
            'id_kategori'           => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'tanggal_dibuat'        => ['required', 'date'],
            'is_aktif'              => ['nullable', 'boolean'],
            'unit_kerja'            => ['required', 'array', 'min:1'],
            'unit_kerja.*'          => ['integer', 'exists:top_unit_kerja,id_unit'],

            // Validasi Baseline Inherent & Target TW1-4
            'inherent'              => ['required', 'integer', 'between:1,25'],
            'target_tw1'            => ['required', 'integer', 'between:1,25'],
            'target_tw2'            => ['required', 'integer', 'between:1,25'],
            'target_tw3'            => ['required', 'integer', 'between:1,25'],
            'target_tw4'            => ['required', 'integer', 'between:1,25'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_peristiwa_risiko.required' => 'Nama peristiwa risiko wajib diisi.',
            'id_kategori.required'           => 'Kategori risiko wajib dipilih.',
            'id_kategori.exists'             => 'Kategori risiko yang dipilih tidak valid.',
            'tanggal_dibuat.required'        => 'Tanggal dibuat wajib diisi.',
            'unit_kerja.required'            => 'Pilih minimal satu unit kerja.',
            'inherent.required'              => 'Nilai inherent awal wajib diisi (1-25).',
            'inherent.between'               => 'Nilai inherent awal harus di antara 1 - 25.',
            'target_tw1.required'            => 'Target TW1 wajib diisi.',
            'target_tw2.required'            => 'Target TW2 wajib diisi.',
            'target_tw3.required'            => 'Target TW3 wajib diisi.',
            'target_tw4.required'            => 'Target TW4 wajib diisi.',
        ];
    }
}
