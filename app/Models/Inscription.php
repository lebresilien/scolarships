<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'student_id',
        'classroom_id'
    ];

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

    public function academy() {
        return $this->belongsTo(Academy::class);
    }
}
