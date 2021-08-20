<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Academy extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'status'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
