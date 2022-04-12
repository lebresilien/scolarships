<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
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

    public function groups() {
        return $this->hasMany(Group::class);
    }
}
