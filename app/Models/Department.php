<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['code', 'name', 'head', 'college_id'];

    public function college()
    {
        return $this->belongsTo(College::class);
    }
}