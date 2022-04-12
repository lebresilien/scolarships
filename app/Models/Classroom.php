<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'group_id',
        'name',
        'slug',
        'description',
        'status',
    ];

    public function building() {
        return $this->belongsTo(Building::class);
    } 

    public function group() {
        return $this->belongsTo(Group::class);
    } 

    public function students() {
        return $this->belongsToMany(Student::class, 'inscriptions');
    }
}
