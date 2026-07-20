<?php

namespace App\Observers;

use App\Models\DepMonitoring;
use App\Helpers\LogHelper;

class DepartemenRiskObserver
{
    public function created(DepMonitoring $risk): void
    {
        LogHelper::aktivitas('Departemen', 'Menambahkan Risk Department baru.');
    }

    public function updated(DepMonitoring $risk): void
    {
        LogHelper::aktivitas('Departemen', 'Memperbarui data Risk Department.');
    }

    public function deleted(DepMonitoring $risk): void
    {
        LogHelper::aktivitas('Departemen', 'Menghapus data Risk Department.');
    }
}
