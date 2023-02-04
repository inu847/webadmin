<?php

namespace App;

use App\FoodCourt;
use App\Models\Catalog;
use Illuminate\Database\Eloquent\Model;

class FoodCourtCatalog extends Model
{
    protected $table = 'food_court_catalogs';
    protected $guard = [];
    protected $fillable = ['food_court_id', 'catalog_id', 'status'];

    public function catalog()
    {
        return $this->belongsTo(Catalog::class, 'catalog_id', 'id');
    }

    public function foodcourt()
    {
        return $this->belongsTo('App\FoodCourt', 'food_court_id', 'id');
    }
}
