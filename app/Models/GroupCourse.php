<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCourse extends Model
{
    use HasFactory;

    protected $table = 'group_course';

    protected $fillable = [
        'group_id',
        'course_id'
    ];
}
