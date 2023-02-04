<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateFoodcourt extends Model
{
    protected $fillable = ['id', 'foodcourt_catalog_id', 'affiliate_percent'];

    public function foodcourtCatalog()
    {
        return $this->belongsTo(FoodCourtCatalog::class, 'foodcourt_catalog_id', 'id');
    }
}
