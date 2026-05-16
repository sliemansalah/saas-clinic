<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

// 🚀 👈 الاستدعاءات الصحيحة للموديلات والمراقبين لمنع الخطأ
use App\Models\Doctor;
use App\Models\Tenant;
use App\Observers\DoctorObserver;
use App\Observers\TenantObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // 🟢 تفعيل كاميرات المراقبة التلقائية للكاش بنجاح وبدون أخطاء
        Doctor::observe(DoctorObserver::class);
        Tenant::observe(TenantObserver::class); 
    }
}
