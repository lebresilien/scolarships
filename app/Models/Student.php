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
        'lname',
        'fathername',
        'mothername',
        'fphone',
        'mphone',
        'born_at',
        'allergie',
        'logo',
        'description',
        'quarter',
        'status'
    ];

    public function classes()
    {
        return $this->belongsToMany(Classes::class);
    }
}
