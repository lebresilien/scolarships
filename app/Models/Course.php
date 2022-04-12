<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function groups() {
        return $this->belongsToMany(Group::class, 'group_course');
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];
}
