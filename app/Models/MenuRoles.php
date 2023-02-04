<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuRoles extends Model
{
    protected $table = 'menu_roles';
    // protected $guard = ['id'];
    protected $fillable = ['status', 'user_id', 'menu_id', 'role_id', 'menu_category_id'];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
