<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'sequence_id',
        'classroom_id',
        'student_id',
        'course_id',
        'value',
        'status'
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function sequence() {
        return $this->belongsTo(Sequence::class);
    }

    public function classroom() {
        return $this->belongsTo(Classroom::class);
    }
}
