<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function catalog(){
        return $this->belongsTo('App\Models\Catalog', 'catalog_id');
	}

    public function bank(){
        return $this->belongsTo('App\Models\NewBank', 'bank_code');
	}

}
