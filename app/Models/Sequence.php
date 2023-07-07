<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'academy_id',
        'name',
        'slug',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function academy() {
        return $this->belongsTo(Academy::class);
    }
}
