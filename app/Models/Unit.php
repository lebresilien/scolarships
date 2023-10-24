<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'slug',
        'description',
        'status'
    ];

    //protected $with = ['courses'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function courses() {
        return $this->hasMany(Course::class);
    }
}
