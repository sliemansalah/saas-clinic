<?php

namespace App\Observers;

use App\Models\Doctor;

use Illuminate\Support\Facades\Cache;

class DoctorObserver
{
    /**
     * Handle the Doctor "created" event.
     */
    public function created(Doctor $doctor): void
    {
        Cache::forget('all_doctors');
    }

    /**
     * Handle the Doctor "updated" event.
     */
    public function updated(Doctor $doctor): void
    {
        Cache::forget('all_doctors');
    }

    /**
     * Handle the Doctor "deleted" event.
     */
    public function deleted(Doctor $doctor): void
    {
         Cache::forget('all_doctors');
    }

    /**
     * Handle the Doctor "restored" event.
     */
    public function restored(Doctor $doctor): void
    {
         Cache::forget('all_doctors');
    }

    /**
     * Handle the Doctor "force deleted" event.
     */
    public function forceDeleted(Doctor $doctor): void
    {
         Cache::forget('all_doctors');
    }
}
