<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'amount',
        'name',
    ];

    public function inscription() {
        return $this->belongsTo(Inscription::class);
    }
}
