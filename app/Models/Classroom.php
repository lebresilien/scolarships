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

    protected $appends = ['building_name', 'group_name'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function building() {
        return $this->belongsTo(Building::class);
    } 

    public function group() {
        return $this->belongsTo(Group::class);
    } 

    public function students() {
        return $this->belongsToMany(Student::class, 'inscriptions')->withPivot('status', 'academy_id');
    }

    public function getBuildingNameAttribute() {
        return $this->building->name;
    }

    public function getGroupNameAttribute() {
        return $this->group->name;
    }

    public function notes() {
        return $this->hasMany(Note::class);
    }
}
