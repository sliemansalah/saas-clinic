<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        // تطبيق حارس جلب البيانات تلقائيًا
        static::addGlobalScope(new TenantScope);

        // حماية الإدخال: تضع معرف العيادة تلقائيًا عند إنشاء سجل جديد
        static::creating(function ($model) {
            if (session()->has('tenant_id') && ! $model->tenant_id) {
                $model->tenant_id = session()->get('tenant_id');
            }
        });
    }
}
