<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'room',
        'subject_code',
        'day',
        'time_in',
        'time_out',
    ];
}