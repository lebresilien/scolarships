<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    use HasFactory;

    protected $fillable = [
        'valid_until_at',
        'inscription_id'
    ];

   /*  public function user() {
        return $this->belongsTo(User::class);
    } */

    public function inscription() {
        return $this->belongsTo(Inscription::class);
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'valid_until_at' => 'datetime:Y-m-d',
    ];

    public function getStatusAttribute($value) {
        return $value ? "Valide" : "Expiré";
    }

}
