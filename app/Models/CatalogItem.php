<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogItem extends Model
{
    protected $table = 'catalog_items';
    protected $guarded = ['id'];

    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id', 'id');
    }

    public function catalog()
    {
        return $this->belongsTo(Catalog::class, 'catalog_id', 'id');
    }
}
