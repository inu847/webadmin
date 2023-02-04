<?php

namespace App;

use App\FoodCourtCatalog;
use App\Models\Catalog;
use App\User;
use Illuminate\Database\Eloquent\Model;

class FoodCourt extends Model
{
    protected $table = 'food_courts';
    protected $guard = [];
    protected $fillable = ['user_id', 'name', 'owner', 'address'];
    
    public function foodcourtCatalog()
    {
        return $this->hasOne(FoodCourtCatalog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    } 

}
