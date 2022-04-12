<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'invitation_token',
        'role_id',
        'classroom_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function generateInvitationToken() {
        $this->invitation_token = substr(md5(rand(0, 9) . $this->email . time()), 0, 32);
    }

    public function classroom() {
        return $this->belongsTo(Classroom::class);
    }

    public function getRole() {
        $role = Role::find($this->role_id);
        return $role->name;
    }
}
