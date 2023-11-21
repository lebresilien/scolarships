<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sequence_id',
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

    public function course() {
        return $this->belongsTo(Course::class);
    }
}
