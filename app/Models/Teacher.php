<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'classe_id',
        'fname',
        'lname',
        'cni',
        'phone',
        'email'
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }
}
