<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Absent extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'inscription_id',
        'day',
        'hour',
        'status'
    ];
}
