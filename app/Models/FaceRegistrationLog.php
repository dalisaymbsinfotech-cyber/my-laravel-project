<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceRegistrationLog extends Model
{
    protected $fillable = [
        'id_number',
        'name',
        'face_id',
        'enrollment_id',
    ];
}
