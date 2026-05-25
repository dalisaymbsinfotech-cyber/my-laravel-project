<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'id_number',
        'name',
        'section',
        'face_scanned',
    ];
}