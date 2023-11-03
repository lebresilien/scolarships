<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscription extends Model
{
    use HasFactory, softDeletes;

   /*  protected $fillable = [
        'academy_id',
        'student_id',
        'classroom_id'
    ]; */

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function academy() {
        return $this->belongsTo(Academy::class);
    }

    public function extensions() {
        return $this->hasMany(Extension::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function classroom() {
        return $this->belongsTo(Classroom::class);
    }
}
