<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $fillable = ['body'];

    /**
     * جلب الموديل الأب الذي ينتمي إليه هذا التعليق (عيادة، موعد، إلخ)
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
