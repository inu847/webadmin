<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogType extends Model
{
    public function catalog_list(){
        return $this->hasMany('App\Models\CatalogListType', 'catalog_list_type_id');
	}
}
