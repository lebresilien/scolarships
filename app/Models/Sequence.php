<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sequence extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academy_id',
        'name',
        'slug',
        'description',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function academy() {
        return $this->belongsTo(Academy::class);
    }

    public function notes() {
        return $this->hasMany(Note::class);
    }
}
