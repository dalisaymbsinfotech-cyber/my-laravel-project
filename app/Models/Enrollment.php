<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'student_name',
        'subject_code',
        'section',
        'face_registration_log_id',
    ];
}