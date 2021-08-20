<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'immatriculation',
        'description',
        'devise_fr',
        'devise_en',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function academys()
    {
        return $this->hasMany(Academy::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
