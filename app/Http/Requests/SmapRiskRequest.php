<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SmapRiskRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan melakukan request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi dinamis berdasarkan action route / method.
     */
    public function rules(): array
    {
        $action =$this->route()->getActionMethod();

        return match ($action) {
            // 1. Validasi untuk Tambah Risk Baru (store)
            'store' => [
                'id_unit'         => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
                'id_kategori'     => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
                'risk_event_deta' => ['required', 'string'],
                'status'          => ['required', 'boolean'],
                'created_at'      => ['required', 'date'],
                'inherent'        => ['required', 'integer', 'min:1', 'max:25'],
                'id_level'        => ['required', 'integer', 'exists:level_risiko,id_level'],
                'inherent_target' => ['required', 'integer', 'min:1', 'max:25'],
                'id_level_target' => ['required', 'integer', 'exists:level_risiko,id_level'],
            ],

            // 2. Validasi untuk Edit Risk Master (update)
            'update' => [
                'risk_event_deta'   => ['required', 'string'],
                'id_kategori'       => ['required', 'integer', 'exists:kategori_risiko,id_kategori'],
                'created_at'        => ['required', 'date'],
                'inherent'          => ['required', 'integer', 'between:1,25'],
                'inherent_target'   => ['required', 'integer', 'between:1,25'],
                'status'            => ['required', 'string', 'in:0,1'],
                'id_unit'           => ['required', 'integer', 'exists:top_unit_kerja,id_unit'],
            ],

            // 3. Validasi untuk Tambah Monitoring Triwulan (storeMonitoring)
            'storeMonitoring' => [
                'quarter'           => ['required', 'in:TW1,TW2,TW3,TW4'],
                'year'              => ['required', 'numeric', 'min:2020', 'max:2099'],
                'value'             => ['required', 'numeric', 'min:1', 'max:25'],
                'status_monitoring' => ['required', 'in:0,1'],
                'status_penanganan' => ['required', 'in:belum,proses,selesai'],
            ],

            // 4. Validasi untuk Update Monitoring Triwulan (updateMonitoring)
            'updateMonitoring' => [
                'quarter'           => ['required', 'in:TW1,TW2,TW3,TW4'],
                'year'              => ['required', 'numeric', 'min:2020', 'max:2099'],
                'value'             => ['required', 'integer', 'between:1,25'],
                'status_penanganan' => ['required', 'string', 'in:belum,proses,selesai'],
                'status'            => ['required', 'in:0,1'],
            ],

            default => [],
        };
    }
}
