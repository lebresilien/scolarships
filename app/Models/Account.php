<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'immatriculation',
        'description',
        'devise_fr',
        'devise_en'
    ];

    public function users() {
        return $this->belongsToMany(User::class, 'user_account');
    }

    public function sections() {
        return $this->hasMany(Section::class);
    }

    public function buildings() {
        return $this->hasMany(Building::class);
    }

    public function academies()
    {
        return $this->hasMany(Academy::class);
    }
}
