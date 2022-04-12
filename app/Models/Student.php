<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'matricule',
        'fname',
        'lname',
        'slug',
        'sexe',
        'father_name',
        'mother_name',
        'fphone',
        'mphone',
        'born_at',
        'born_place',
        'allergy',
        'logo',
        'description',
        'quarter',
        'status'
    ];

    public function classrooms() {
        return $this->belongsToMany(Classroom::class, 'inscriptions');
    }

}
