<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Doctor extends Model
{
    protected $fillable = ['name', 'specialty'];

    /**
     * علاقة متعدد لمتعدد: الطبيب يملك العديد من المواعيد/المرضى
     */
    public function appointments(): BelongsToMany
    {
        // نستخدم belongsToMany للربط الشبكي متعدد لمتعدد
        return $this->belongsToMany(Appointment::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }


}
