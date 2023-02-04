<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemAddon extends Model
{
    protected $fillable = ['user_id', 'category_id', 'item_id', 'addons_id', 'catalog_id', 'check_type'];

    public function addons()
    {
        return $this->belongsTo(Addon::class, 'addons_id', 'id');
    }
}
