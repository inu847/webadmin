<?php

namespace App\Models;

use App\FoodCourt;
use App\Models\Catalog;
use Illuminate\Database\Eloquent\Model;

class RequestCatalog extends Model
{
    protected $table = 'request_catalogs';
    protected $guard = [];
    protected $fillable = ['food_court_id', 'catalog_id', 'status', 'user_id'];

    public function catalog()
    {
        return $this->belongsTo(Catalog::class, 'catalog_id', 'id');
    }

    public function foodcourt()
    {
        return $this->belongsTo(FoodCourt::class, 'foodcourt_id', 'id');
    }
}
