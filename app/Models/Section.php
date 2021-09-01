<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'school_id',
        'name',
        'slug',
        'description',
        'status'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    public function groupe()
    {
        return $this->hasMany(Groupe::class);
    }
}
