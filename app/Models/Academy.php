<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Academy extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'name',
        'slug',
        'status'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    
    public function inscriptions() {
        return $this->hasMany(Inscription::class);
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];
}
