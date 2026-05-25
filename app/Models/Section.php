<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'section_code', 'department_id', 'college_id'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }
}