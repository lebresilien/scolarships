<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Section extends Model
{
    use HasFactory, HasRelationships;

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

    public function notes()
    {
        return $this->hasManyDeep(Note::class, [Group::class, Classroom::class]);
    }
}
