<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'subject_code',
        'subject_name',
        'professor_name',
        'section',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}