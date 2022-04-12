<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'name',
        'slug',
        'description',
        'status'
    ];

    public function account() {
        return $this->belongsTo(Account::class);
    } 

    public function classrooms() {
        return $this->belongsTo(Classroom::class);
    }
}
