<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_id',
        'name',
        'slug',
        'description',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function account() {
        return $this->belongsTo(Account::class);
    } 

    public function classrooms() {
        return $this->hasMany(Classroom::class);
    }
}
