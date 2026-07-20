<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepMonitoringRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan membuat request ini.
     */
    public function authorize(): bool
    {
        return true; // Set ke true agar bisa diakses
    }

    /**
     * Aturan validasi yang diterapkan ke request.
     */
    public function rules(): array
    {
        return [
            'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
            'risk_event_deta' => ['required', 'string'],
            'status'          => ['required', 'boolean'],
            'type'            => ['required', 'in:Proyek,Non-Proyek'],
            'inherent'        => ['required', 'numeric', 'min:1', 'max:25'],
            'id_level'        => ['required', 'integer'],

            // --- UBAH DUA BARIS DI BAWAH INI ---
            'target_value'    => ['required', 'numeric', 'min:1', 'max:25'],
            'target_id_level' => ['required', 'integer'],
        ];
    }

    /**
     * (Opsional) Custom pesan error jika validasi gagal
     */
    public function messages(): array
    {
        return [
            'id_unit.required' => 'Unit Kerja wajib diisi.',
            'inherent.required' => 'Nilai Inheren wajib diisi.',
            // Anda bisa tambahkan pesan kustom lainnya di sini...
        ];
    }
}
