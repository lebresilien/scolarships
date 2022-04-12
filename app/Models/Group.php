<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'name',
        'slug',
        'description',
        'status',
        'fees'
    ];

    public function section() {
        return $this->belongsTo(Section::class);
    }

    public function classrooms() {
        return $this->hasMany(Classroom::class);
    }

    public function courses() {
        return $this->belongsToMany(Course::class, 'group_course');
    }

}
