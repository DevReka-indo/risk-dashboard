<?php

namespace App\Observers;

use App\Models\SmapMonitoring;
use App\Helpers\LogHelper;

class SmapMonitoringObserver
{
    public function created(SmapMonitoring $smap): void
    {
        LogHelper::aktivitas('Risk SMAP', 'Menambahkan Risk SMAP baru (ID: ' . $smap->id_smap . ').');
    }

    public function updated(SmapMonitoring $smap): void
    {
        LogHelper::aktivitas('Risk SMAP', 'Memperbarui data Risk SMAP (ID: ' . $smap->id_smap . ').');
    }

    public function deleted(SmapMonitoring $smap): void
    {
        LogHelper::aktivitas('Risk SMAP', 'Menghapus data Risk SMAP (ID: ' . $smap->id_smap . ').');
    }
}
