<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

        /**
     * الموعد الواحد/المريض يمكن أن يشرف عليه أكثر من طبيب في نفس الوقت
     */
    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class);
    }


    /**
     * جلب جميع الملاحظات الخاصة بهذا الموعد
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        // نستخدم morphMany لأن الموعد يمكن أن يملك "كثير" من الملاحظات متعددة الأشكال
        return $this->morphMany(Comment::class, 'commentable');
    }




}
