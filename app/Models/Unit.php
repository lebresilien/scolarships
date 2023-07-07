<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'name',
        'slug',
        'description',
        'status'
    ];

    //protected $with = ['courses'];

    public function account() {
        return $this->belongsTo(Account::class);
    }

    public function courses() {
        return $this->hasMany(Course::class);
    }
}
