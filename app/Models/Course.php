<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'unit_id',
        'coeff'
    ];

    public function groups() {
        return $this->belongsToMany(Group::class, 'group_course');
    }

    public function unit() {
        return $this->belongsTo(Unit::class);
    }

    public function notes() {
        return $this->hasMany(Note::class);
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];
}
