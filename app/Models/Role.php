<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $guard = [];
    protected $fillable = ['name','owner_id'];

    public function menuRole()
    {
        return $this->hasMany(MenuRoles::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
}
