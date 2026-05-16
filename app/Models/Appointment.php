<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use BelongsToTenant; // تفعيل العزل التلقائي والمستمر

    protected $fillable = ['patient_name', 'patient_phone', 'appointment_date'];
}
