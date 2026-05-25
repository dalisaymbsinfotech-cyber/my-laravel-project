<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['room_name', 'room_code', 'building', 'capacity', 'description', 'status'];

    public function schedules()
    {
        return $this->hasMany(ClassroomSchedule::class);
    }
}