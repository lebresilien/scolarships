<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_id',
        'groupe_id',
        'name',
        'description',
        'status'
    ];

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }

}
