<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = ['name', 'domain', 'notification_preference'];

    /**
     * علاقة واحد لمتعدد: العيادة تملك العديد من المواعيد
     */
    public function appointments(): HasMany
    {
        // نستخدم hasMany لأن العيادة تملك "كثير" من المواعيد
        return $this->hasMany(Appointment::class);
    }
}
