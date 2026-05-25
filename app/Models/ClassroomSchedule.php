<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ClassroomSchedule extends Model
{
    protected $fillable = ['classroom_id', 'academic_year', 'semester', 'day', 'room_no', 'date_of_use', 'time_in', 'time_out', 'description'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}