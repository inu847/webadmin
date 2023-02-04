<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ItemIn extends Model
{
    protected $table = 'item_ins';

    protected $fillable = ['user_id', 'catalog_id', 'item_id', 'ingredient_id', 'stock', 'notes', 'addons_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }

    public function addons()
    {
        return $this->belongsTo(Addon::class, 'addons_id', 'id');
    }
}
