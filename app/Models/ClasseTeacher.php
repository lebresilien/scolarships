<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClasseTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'classe_id',
        'academy_id',
    ];
}
