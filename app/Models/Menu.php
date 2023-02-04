<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $guard = [];
    protected $fillable = ['name','url','active','parent_id','order_view','is_mobile','menu_category_id'];

    public function menuRole()
    {
        return $this->hasMany(MenuRoles::class);
    }

    public function category()
    {
        return $this->belongsTo(Menu::class);
    }

}
