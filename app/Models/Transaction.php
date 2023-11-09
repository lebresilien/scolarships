<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'inscription_id',
        'amount',
        'name',
    ];

    public function inscription() {
        return $this->belongsTo(Inscription::class);
    }

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];
}
