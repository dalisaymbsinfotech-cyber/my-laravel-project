<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'company_name',
        'system_name',
        'logo_path',
        'admin_username',
        'admin_password',
    ];
}