<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    protected $guarded = ['id'];

    public function menu()
    {
        return $this->hasMany(Menu::class);
    }
}
