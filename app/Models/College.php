<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    protected $fillable = ['code', 'name', 'dean'];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}