<?php

namespace App\Observers;

use App\Models\Tenant;

class TenantObserver
{
    /**
     * Handle the Tenant "created" event.
     */
    public function created(Tenant $tenant): void
    {
        Cache::forget('all_tenants');
    }

    /**
     * Handle the Tenant "updated" event.
     */
    public function updated(Tenant $tenant): void
    {
        Cache::forget('all_tenants');
    }

    /**
     * Handle the Tenant "deleted" event.
     */
    public function deleted(Tenant $tenant): void
    {
        Cache::forget('all_tenants');

    }

    /**
     * Handle the Tenant "restored" event.
     */
    public function restored(Tenant $tenant): void
    {
        Cache::forget('all_tenants');

    }

    /**
     * Handle the Tenant "force deleted" event.
     */
    public function forceDeleted(Tenant $tenant): void
    {
        Cache::forget('all_tenants');
    }
}
