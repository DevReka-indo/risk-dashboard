<?php

namespace App\Observers;

use App\Models\TopRisiko;
use App\Helpers\LogHelper;

class TopRisikoObserver
{
    public function created(TopRisiko $topRisiko): void
    {
        LogHelper::aktivitas(
            'Top Risk',
            'Menambahkan Top Risk baru: <strong>"' . $topRisiko->nama_peristiwa_risiko . '"</strong>.'
        );
    }

    public function updated(TopRisiko $topRisiko): void
    {
        LogHelper::aktivitas(
            'Top Risk',
            'Memperbarui Top Risk: <strong>"' . $topRisiko->nama_peristiwa_risiko . '"</strong>.'
        );
    }

    public function deleted(TopRisiko $topRisiko): void
    {
        LogHelper::aktivitas(
            'Top Risk',
            'Menghapus Top Risk: <strong>"' . $topRisiko->nama_peristiwa_risiko . '"</strong>.'
        );
    }
}
