<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use BelongsToTenant; // تفعيل العزل التلقائي والمستمر

    protected $fillable = ['patient_name', 'patient_phone', 'appointment_date'];

      /**
     * العلاقة العكسية: الموعد ينتمي لـ عيادة واحدة فقط
     */
    public function tenant(): BelongsTo
    {
        // نستخدم belongsTo لأن الموعد "ينتمي إلى" عيادة محددة
        return $this->belongsTo(Tenant::class);
    }

}
